function mymodule_uninstall() {
  \Drupal::configFactory()->getEditable('mymodule.settings')->delete();
}

lando drush migrate-upgrade --legacy-db-key=migrate --legacy-root=https://htlf.ddev.site:8443/


Results as of 8/18/2023

 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- --------------------- 
  Group         Migration ID                                Status   Total   Imported   Unprocessed   Message Count   Last Imported        
 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- --------------------- 
  HTLF (htlf)   migrate_files                               Idle     7881    7821       0             60              2023-08-18 11:31:44  
  HTLF (htlf)   migrate_paragraph_accordion_tab_item        Idle     634     0          634           0                                    
  HTLF (htlf)   migrate_paragraph_accordions_tabs           Idle     82      0          82            0                                    
  HTLF (htlf)   migrate_paragraph_banner_item               Idle     31      0          31            0                                    
  HTLF (htlf)   migrate_paragraph_blog_posts                Idle     0       0          0             0                                    
  HTLF (htlf)   migrate_paragraph_general_content_section   Idle     1279    0          1279          0                                    
  HTLF (htlf)   migrate_paragraph_ordered_team_members      Idle     110     0          110           0                                    
  HTLF (htlf)   migrate_paragraph_rate_item                 Idle     56      0          56            0                                    
  HTLF (htlf)   migrate_paragraph_sidebar_content_section   Idle     886     0          886           0                                    
  HTLF (htlf)   migrate_paragraph_static_map                Idle     0       0          0             0                                    
  HTLF (htlf)   migrate_paragraph_uneven_columns            Idle     104     0          104           0                                    
  HTLF (htlf)   migrate_paragraph_webform                   Idle     79      0          79            0                                    
  HTLF (htlf)   migrate_taxonomy_blog_post_category         Idle     3       0          3             0                                    
  HTLF (htlf)   migrate_taxonomy_blog_tags                  Idle     58      0          58            0                                    
  HTLF (htlf)   migrate_taxonomy_member_group               Idle     18      0          18            0                                    
  HTLF (htlf)   migrate_url_alias                           Idle     1856    0          1856          0                                    
  HTLF (htlf)   migrate_user                                Idle     84      83         0             1               2023-08-17 13:30:39  
  HTLF (htlf)   migrate_media                               Idle     7881    7821       0             0               2023-08-18 11:39:40  
  HTLF (htlf)   migrate_node_blog_post                      Idle     356     0          356           0                                    
  HTLF (htlf)   migrate_paragraph_banner                    Idle     17      0          17            0                                    
  HTLF (htlf)   migrate_paragraph_rates                     Idle     8       0          8             0                                    
  HTLF (htlf)   migrate_node_landing_page                   Idle     20      0          20            0                                    
  HTLF (htlf)   migrate_paragraph_testimonial               Idle     9       0          9             0                                    
  HTLF (htlf)   migrate_node_alert                          Idle     42      0          42            0                                    
  HTLF (htlf)   migrate_paragraph_featured_product          Idle     8       0          8             0                                    
  HTLF (htlf)   migrate_node_team_members                   Idle     540     0          540           0                                    
  HTLF (htlf)   migrate_paragraph_call_to_action            Idle     33      0          33            0                                    
  HTLF (htlf)   migrate_node_page                           Idle     359     0          359           0                                    
  HTLF (htlf)   migrate_paragraph_grid_item                 Idle     1948    0          1948          0                                    
  HTLF (htlf)   migrate_node_location                       Idle     330     0          330           0                                    
  HTLF (htlf)   migrate_paragraph_grid                      Idle     487     0          487           0                                    
 ------------- ------------------------------------------- -------- ------- ---------- ------------- --------------- --------------------- 