function mymodule_uninstall() {
  \Drupal::configFactory()->getEditable('mymodule.settings')->delete();
}

lando drush migrate-upgrade --legacy-db-key=migrate --legacy-root=https://htlf.ddev.site:8443/

Notes:
Direct Paragraph Relations are 1:1, there is no need to do a migration_look up


Results as of 11/28/2023

 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- ---------------------
  Group         Migration ID                                Status   Total   Imported   Unprocessed   Message Count   Last Imported
 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- ---------------------
  HTLF (htlf)   migrate_files                               Idle     8114    8054       0             60              2023-11-22 11:47:20
  HTLF (htlf)   migrate_menu_items                          Idle     272     0          0             0               2023-11-22 12:30:33
  HTLF (htlf)   migrate_paragraph_accordion_tab_item        Idle     635     635        0             0               2023-11-22 12:26:10
  HTLF (htlf)   migrate_paragraph_accordions_tabs           Idle     82      82         0             0               2023-11-22 12:28:45
  HTLF (htlf)   migrate_paragraph_banner                    Idle     17      17         0             0               2023-11-22 12:28:32
  HTLF (htlf)   migrate_paragraph_banner_item               Idle     31      31         0             0               2023-11-22 12:25:47
  HTLF (htlf)   migrate_paragraph_blog_posts                Idle     144     144        0             0               2023-11-28 10:22:23
  HTLF (htlf)   migrate_paragraph_call_to_action            Idle     33      33         0             0               2023-11-22 12:27:25
  HTLF (htlf)   migrate_paragraph_featured_product          Idle     10      10         0             0               2023-11-22 12:24:18
  HTLF (htlf)   migrate_paragraph_general_content_section   Idle     1322    1322       0             0               2023-11-22 12:29:38
  HTLF (htlf)   migrate_paragraph_grid                      Idle     488     488        0             0               2023-11-22 12:28:07
  HTLF (htlf)   migrate_paragraph_grid_item                 Idle     1953    1953       0             0               2023-11-22 12:26:51
  HTLF (htlf)   migrate_paragraph_ordered_team_members      Idle     113     113        0             0               2023-11-22 12:25:20
  HTLF (htlf)   migrate_paragraph_rate_item                 Idle     56      56         0             0               2023-11-22 12:25:35
  HTLF (htlf)   migrate_paragraph_sidebar_content_section   Idle     933     933        0             0               2023-11-22 12:29:57
  HTLF (htlf)   migrate_paragraph_static_map                Idle     0       0          0             0               2023-11-22 12:23:55
  HTLF (htlf)   migrate_paragraph_team_members              Idle     13      13         0             0               2023-11-22 12:24:29
  HTLF (htlf)   migrate_paragraph_testimonial               Idle     9       9          0             0               2023-11-22 12:24:44
  HTLF (htlf)   migrate_paragraph_uneven_columns            Idle     106     106        0             0               2023-11-22 12:29:16
  HTLF (htlf)   migrate_paragraph_webform                   Idle     83      83         0             0               2023-11-22 12:23:46
  HTLF (htlf)   migrate_redirects                           Idle     1546    1546       0             0               2023-11-22 12:43:20
  HTLF (htlf)   migrate_taxonomy_blog_post_category         Idle     3       3          0             0               2023-11-22 12:17:51
  HTLF (htlf)   migrate_taxonomy_blog_post_tags             Idle     59      59         0             0               2023-11-22 12:18:06
  HTLF (htlf)   migrate_taxonomy_member_group               Idle     18      18         0             0               2023-11-22 12:18:12
  HTLF (htlf)   migrate_url_alias                           Idle     1986    1986       0             0               2023-11-22 12:41:18
  HTLF (htlf)   migrate_paragraph_rates                     Idle     8       8          0             0               2023-11-22 12:27:38
  HTLF (htlf)   migrate_user                                Idle     87      86         0             1               2023-11-22 12:19:18
  HTLF (htlf)   migrate_node_blog_post                      Idle     391     391        0             0               2023-11-22 12:21:25
  HTLF (htlf)   migrate_node_landing_page                   Idle     20      20         0             0               2023-11-22 12:20:50
  HTLF (htlf)   migrate_node_alert                          Idle     48      48         0             0               2023-11-22 12:20:31
  HTLF (htlf)   migrate_node_team_members                   Idle     593     593        0             0               2023-11-22 12:21:10
  HTLF (htlf)   migrate_node_page                           Idle     362     362        0             0               2023-11-22 12:21:39
  HTLF (htlf)   migrate_media                               Idle     8114    8054       0             0               2023-11-27 13:23:42
  HTLF (htlf)   migrate_node_location                       Idle     337     337        0             0               2023-11-22 12:23:07
 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- ---------------------

##web/core/lib/Drupal/Core/TypedData/Plugin/DataType/Map.php
##$backup_array = ['value' => 'a:0:{}'];
##$values = $values == "a:0:{}" ? $backup_array : $values;