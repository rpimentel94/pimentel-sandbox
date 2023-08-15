function mymodule_uninstall() {
  \Drupal::configFactory()->getEditable('mymodule.settings')->delete();
}

lando drush migrate-upgrade --legacy-db-key=migrate --legacy-root=https://htlf.ddev.site:8443/