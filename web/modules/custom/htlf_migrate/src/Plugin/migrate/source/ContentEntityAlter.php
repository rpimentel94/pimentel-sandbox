<?php

namespace Drupal\htlf_migrate\Plugin\migrate\source;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Base class for D8 source plugins to collect field values from Field API.
 *
 * @MigrateSource(
 *   id = "d8_entity_alter",
 *   source_provider = "htlf_migrate"
 * )
 */
class ContentEntityAlter extends SqlBase
{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Static cache for bundle fields.
   *
   * @var array
   */
  protected $bundleFields = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager)
  {
    if (empty($configuration['entity_type'])) {
      throw new InvalidPluginDefinitionException($plugin_id, 'Missing required entity_type definition');
    }
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    parent::__construct($configuration + ['bundle' => NULL], $plugin_id, $plugin_definition, $migration, $state);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * Returns all non-deleted field instances attached to a specific entity type.
   *
   * @param string $entity_type
   *   The entity type ID.
   * @param string|null $bundle
   *   (optional) The bundle.
   *
   * @todo
   *   I am not sure that we even need that. Would we be doing it wrong if
   *   we relied only on table names?
   *
   * @return array[]
   *   The field instances, keyed by field name.
   */
  protected function getFields($entity_type, $bundle = NULL)
  {
    $fieldConfig = $this->select('config', 'c')
      ->fields('c')
      ->condition('name', 'field.field.' . $entity_type . '.%', 'LIKE')
      ->execute()
      ->fetchAllAssoc('name');

    $fields = [];
    foreach ($fieldConfig as $config) {
      $data = unserialize($config['data']);
      // Status of false signifies the field is deleted. We do not return
      // deleted data.
      if ($data['status']) {
        // If requested by configuration, filter by a bundle. Don't filter
        // if it isn't configured.
        if ($bundle && $data['bundle'] == $bundle) {
          $fields[$data['field_name']] = $data;
        } else {
          $fields[$data['field_name']] = $data;
        }
      }
    }

    // Base fields that have a cardinality greater then 1 are their own
    // dedicated tables. We need to gather those names too. But they are in code
    // so this is the only way to get those names. They aren't stored in the DB.
    foreach ($this->entityFieldManager->getBaseFieldDefinitions($this->configuration['entity_type']) as $fieldName => $definition) {
      if ($this->shouldMigrateBaseFieldDefinition($fieldName, $definition)) {
        $fields[$fieldName] = $fieldName;
      }
    }

    return $fields;
  }

  /**
   * Retrieves field values for a single field of a single entity.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $field_name
   *   The field name.
   * @param int $entity_id
   *   The entity ID.
   * @param int|null $revision_id
   *   (optional) The entity revision ID.
   *
   * @throws \Drupal\migrate\MigrateException
   *
   * @return array
   *   The raw field values, keyed by delta.
   *
   * @todo Support multilingual field values.
   */
  protected function getFieldValues($entity_type, $field_name, $entity_id, $revision_id = NULL)
  {
    $table = $this->getDedicatedDataTableName($entity_type, $field_name);

    $query = $this->select($table, 't')
      ->fields('t')
      ->condition('entity_id', $entity_id)
      ->condition('deleted', 0);

    if ($revision_id) {
      $query->condition('revision_id', $revision_id);
    }

    $values = [];
    foreach ($query->execute() as $row) {
      foreach ($row as $key => $value) {
        $delta = $row['delta'];
        if (strpos($key, $field_name) === 0) {
          $column = substr($key, strlen($field_name) + 1);
          $values[$delta][$column] = $value;
        }
      }
    }
    return $values;
  }

  /**
   * Get the table name keeping in mind the hashing logic for long names.
   *
   * @param string $entityType
   *   Entity type id.
   * @param string $field_name
   *   Field name.
   * @param bool $revision
   *   If revision table or not.
   *
   * @see \Drupal\Core\Entity\Sql\DefaultTableMapping::generateFieldTableName
   *
   * @throws \Drupal\migrate\MigrateException
   *
   * @return string
   *   The table name string.
   */
  protected function getDedicatedDataTableName($entityType, $field_name, $revision = FALSE)
  {
    $separator = $revision ? '_revision__' : '__';
    $tableName = $entityType . $separator . $field_name;

    // This matches \Drupal\Core\Entity\Sql\DefaultTableMapping where longer
    // table revision names get shortened by the Entity API.
    if (strlen($tableName) > 48) {
      $separator = $revision ? '_r__' : '__';

      $query = $this->select('config', 'c')
        ->fields('c', ['data'])
        ->condition('name', "field.storage.{$entityType}.{$field_name}");
      $fieldDefinitionData = $query->execute()->fetchField();

      if ($fieldDefinitionData) {
        $data = unserialize($fieldDefinitionData);
        $uuid = $data['uuid'];
      } else {
        throw new MigrateException(sprintf('Missing field storage config for field %s', $field_name));
      }

      $entityType = substr($entityType, 0, 34);
      $fieldHash = substr(hash('sha256', $uuid), 0, 10);
      $tableName = $entityType . $separator . $fieldHash;
    }
    return $tableName;
  }

  /**
   * {@inheritdoc}
   */
  public function query()
  {
    $entityDefinition = $this->entityTypeManager->getDefinition($this->configuration['entity_type']);
    $idKey = $entityDefinition->getKey('id');
    $bundleKey = $entityDefinition->getKey('bundle');
    $baseTable = $entityDefinition->getBaseTable();
    $dataTable = $entityDefinition->getDataTable();

    if ($dataTable) {
      $query = $this->select($dataTable, 'd')
        ->fields('d');
      $alias = $query->innerJoin($baseTable, 'b', "b.{$idKey} = d.{$idKey}");
      $query->fields($alias);
      if (!empty($this->configuration['bundle'])) {
        $query->condition("d.{$bundleKey}", $this->configuration['bundle']);
      }
    } else {
      $query = $this->select($baseTable, 'b')
        ->fields('b');
      if (!empty($this->configuration['bundle'])) {
        $query->condition("b.{$bundleKey}", $this->configuration['bundle']);
      }
    }

    return $query;

  }

  /**
   * {@inheritdoc}
   */
  public function fields()
  {
    $fieldDefinitions = $this->entityFieldManager->getBaseFieldDefinitions($this->configuration['entity_type']);
    $fields = [];
    foreach ($fieldDefinitions as $fieldName => $definition) {
      $fields[$fieldName] = (string) $definition->getLabel();
    }
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row)
  {
    $entityType = $this->configuration['entity_type'];
    $bundleType = $this->configuration['bundle'];
    // Get Field API field values.
    if (!$this->bundleFields) {
      $this->bundleFields = $this->getFields($entityType, $this->configuration['bundle']);
    }

    $entityDefinition = $this->entityTypeManager->getDefinition($this->configuration['entity_type']);
    $idKey = $entityDefinition->getKey('id');
    foreach (array_keys($this->bundleFields) as $field) {
      $entityId = $row->getSourceProperty($idKey);
      $revisionId = NULL;
      if ($entityDefinition->isRevisionable()) {
        $revisionKey = $entityDefinition->getKey('revision');
        $revisionId = $row->getSourceProperty($revisionKey);
      }
      $row->setSourceProperty($field, $this->getFieldValues($entityType, $field, $entityId, $revisionId));
    }

    // Creating Alter for Body field
    $nid = $row->getSourceProperty('nid');
    // body (compound field with value, summary, and format)
    $body_result = $this->getDatabase()->query('
      SELECT
        fld.body_value,
        fld.body_summary,
        fld.body_format
      FROM
        {node__body} fld
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));

    if ($body_result) {
      foreach ($body_result as $record) {
        $row->setSourceProperty('body_value', $record->body_value);
        $row->setSourceProperty('body_summary', $record->body_summary);
        $row->setSourceProperty('body_format', $record->body_format);
      }
    }

    $author_result = $this->getDatabase()->query('
      SELECT
        fld.uid
      FROM
        {node_field_data} fld
      WHERE
        fld.nid = :nid
    ', array(':nid' => $nid));

    if ($author_result) {
      foreach ($author_result as $record) {
        $row->setSourceProperty('uid', $record->uid);
      }
    } else {
      $row->setSourceProperty('uid', 1);
    }

    if ($bundleType = "team_member") {
      $image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_member_image_target_id,
          f.thumbnail__target_id
        FROM
          {node__field_q2_member_image} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_member_image_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('member_image', $media->mid);
        }
      }
    }

    if ($bundleType = "location") {
      $image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_location_image_target_id,
          f.thumbnail__target_id
        FROM
          {node__field_q2_location_image} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_location_image_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('location_image', $media->mid);
        }
      }
    }

    if ($bundleType = "page" || $bundleType = "blog_post") {
      $image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_internal_banner_image_target_id,
          f.thumbnail__target_id
        FROM
          {node__field_q2_internal_banner_image} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_internal_banner_image_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('internal_banner_image', $media->mid);
        }
      }

      $blog_image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_blog_image_target_id,
          f.thumbnail__target_id
        FROM
          {node__field_q2_blog_image} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_blog_image_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($blog_image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('blog_image', $media->mid);
        }
      }
    }

    if ($entityType = "paragraph") {
      $nid = $row->getSourceProperty('id');
      //\Drupal::logger('my_module')->debug('<pre><code>' . print_r($nid, TRUE) . '</code></pre>');

      $image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_image_target_id,
          f.thumbnail__target_id
        FROM
          {paragraph__field_q2_image} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_image_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('image', $media->mid);
        }
      }

      $image_result = $this->getDatabase()->query('
        SELECT
          fld.field_q2_image_secondary_target_id,
          f.thumbnail__target_id
        FROM
          {paragraph__field_q2_image_secondary} fld
        JOIN
          {media_field_data} f 
        ON 
          fld.field_q2_image_secondary_target_id = f.mid
        WHERE
          fld.entity_id = :nid
      ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('second_image', $media->mid);
        }
      }

    } else {
      $image_result = $this->getDatabase()->query('
      SELECT
        fld.field_q2_image_target_id,
        f.thumbnail__target_id
      FROM
        {node__field_q2_image} fld
      JOIN
        {media_field_data} f 
      ON 
        fld.field_q2_image_target_id = f.mid
      WHERE
        fld.entity_id = :nid
    ', array(':nid' => $nid));

      if ($image_result) {
        foreach ($image_result as $record) {
          $media = $this->getNewMediaId($record->thumbnail__target_id) ?: NULL;
          $row->setSourceProperty('image', $media->mid);
        }
      }
    }

    return parent::prepareRow($row);
  }

  public function getNewMediaId($id)
  {
    $database = \Drupal::database();
    $query = $database->query("SELECT * FROM {media_field_data} WHERE thumbnail__target_id = :target_id", [':target_id' => $id,]);
    $media = $query->fetchAll();
    if ($media) {
      foreach ($media as $record) {
        return $record;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getIds()
  {
    $entityDefinition = $this->entityTypeManager->getDefinition($this->configuration['entity_type']);
    $idKey = $entityDefinition->getKey('id');
    $ids[$idKey] = $this->getDefinitionFromEntity($idKey);

    if ($entityDefinition->isTranslatable()) {
      $langcodeKey = $entityDefinition->getKey('langcode');
      $ids[$langcodeKey] = $this->getDefinitionFromEntity($langcodeKey);
    }

    return $ids;
  }

  /**
   * Gets the field definition from a specific entity base field.
   *
   * @param string $key
   *   The field ID key.
   *
   * @return array
   *   An associative array with a structure that contains the field type, keyed
   *   as 'type', together with field storage settings as they are returned by
   *   FieldStorageDefinitionInterface::getSettings().
   *
   * @see \Drupal\migrate\Plugin\migrate\destination\EntityContentBase::getDefinitionFromEntity()
   */
  protected function getDefinitionFromEntity($key)
  {
    /** @var \Drupal\Core\Field\FieldStorageDefinitionInterface[] $fieldDefinitions */
    $fieldDefinitions = $this->entityFieldManager->getBaseFieldDefinitions($this->configuration['entity_type']);
    $fieldDefinition = $fieldDefinitions[$key];

    return [
      'alias' => 'b',
      'type' => $fieldDefinition->getType(),
    ] + $fieldDefinition->getSettings();
  }

  /**
   * Determine if a base field definition should be migrated.
   *
   * @param string $fieldName
   *   The name of the field to evaluate.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $definition
   *   The base field definition to evaluate.
   *
   * @return bool
   *   Return TRUE if the given base field definition should be migrated; FALSE
   *   otherwise.
   */
  protected function shouldMigrateBaseFieldDefinition($fieldName, $definition)
  {
    return $definition instanceof FieldStorageDefinitionInterface
      && $definition->isMultiple()
      && ($definition->isComputed() === FALSE);
  }

}