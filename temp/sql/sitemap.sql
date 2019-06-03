SELECT  family.product_family_1st_product_id AS 'id',

        object.type AS 'type',

        GROUP_CONCAT(DISTINCT
          IF(family.product_family_1st_product_id=object.id, IFNULL(object_meta_i18n_xx.name, object_meta_i18n_en.name), NULL)
        ) AS 'name',

        object.account_name AS 'account_name',

        CONCAT_WS('@--@',

          GROUP_CONCAT(DISTINCT
            IF(ISNULL(object_content.image_gallery_path),
              NULL,
              # separator between path and title for image
              CONCAT_WS('@-@',
                object_content.image_gallery_path,
                CONCAT_WS(' | ',
                  CONCAT(IFNULL(object_meta_i18n_xx.name, object_meta_i18n_en.name),
                    IF('de'='de', ' von ', IF('en'='it', ' di ', IF('en'='es', ' de ', IF('en'='fr', ' de ', ' by ')))),
                    object.account_name
                  ),
                  IFNULL(object_meta_i18n_xx.group_master_name, object_meta_i18n_en.group_master_name)
                )
              )
            )
            ORDER BY object_content.list_order
            # separator between images
            SEPARATOR '@--@'
          ),

          GROUP_CONCAT(DISTINCT
            IF(ISNULL(family_content.image_gallery_path),
              NULL,
              # separator between path and title for image
              CONCAT_WS('@-@',
                family_content.image_gallery_path,
                CONCAT_WS(' | ',
                  CONCAT(IFNULL(family_meta_i18n_xx.name, family_meta_i18n_en.name),
                    IF('de'='de', ' von ', IF('en'='it', ' di ', IF('en'='es', ' de ', IF('en'='fr', ' de ', ' by ')))),
                    object.account_name
                  )
                )
              )
            )
            ORDER BY object_content.list_order
            # separator between images
            SEPARATOR '@--@'
          )

        ) AS 'image_list'

  FROM object AS family

  LEFT JOIN object_content AS family_content ON family_content.object_id=family.id
        AND family_content.type IN ('picture','gallery')
        AND family_content.access_status='online'
  LEFT JOIN object_meta AS family_meta ON family.id=family_meta.object_id
  LEFT JOIN object_meta_i18n AS family_meta_i18n_en ON family_meta.id=family_meta_i18n_en.object_meta_id
        AND family_meta_i18n_en.language='en'
  LEFT JOIN object_meta_i18n AS family_meta_i18n_xx ON family_meta.id=family_meta_i18n_xx.object_meta_id
        AND family_meta_i18n_xx.language='de'

 INNER JOIN object ON object.product_family_id=family.id
  LEFT JOIN object_content ON object_content.object_id=object.id
        AND object_content.type IN ('picture','gallery')
        AND FIND_IN_SET('online', object_content.access_status )
  LEFT JOIN object_meta ON object.id=object_meta.object_id
  LEFT JOIN object_meta_i18n object_meta_i18n_en ON object_meta.id=object_meta_i18n_en.object_meta_id
        AND object_meta_i18n_en.language='en'
  LEFT JOIN object_meta_i18n object_meta_i18n_xx ON object_meta.id=object_meta_i18n_xx.object_meta_id
        AND object_meta_i18n_xx.language='de'

 WHERE FIND_IN_SET('product', object.type)
   AND FIND_IN_SET('online', object.access_status )
   AND FIND_IN_SET('architonic', object.projects_set )
   AND FIND_IN_SET('object', object.attachments_set )

 GROUP BY family.id

 ORDER BY family.id DESC;

############################################################

SELECT  family.product_family_1st_product_id AS 'id',

        object.type AS 'type',

        object.account_name AS 'account_name',

  FROM object AS family

  LEFT JOIN object_content AS family_content ON family_content.object_id=family.id
        AND family_content.type IN ('picture','gallery')
        AND family_content.access_status='online'
  LEFT JOIN object_meta AS family_meta ON family.id=family_meta.object_id
  LEFT JOIN object_meta_i18n AS family_meta_i18n_en ON family_meta.id=family_meta_i18n_en.object_meta_id
        AND family_meta_i18n_en.language='en'
  LEFT JOIN object_meta_i18n AS family_meta_i18n_xx ON family_meta.id=family_meta_i18n_xx.object_meta_id
        AND family_meta_i18n_xx.language='de'

 INNER JOIN object ON object.product_family_id=family.id
  LEFT JOIN object_content ON object_content.object_id=object.id
        AND object_content.type IN ('picture','gallery')
        AND FIND_IN_SET('online', object_content.access_status )
  LEFT JOIN object_meta ON object.id=object_meta.object_id
  LEFT JOIN object_meta_i18n object_meta_i18n_en ON object_meta.id=object_meta_i18n_en.object_meta_id
        AND object_meta_i18n_en.language='en'
  LEFT JOIN object_meta_i18n object_meta_i18n_xx ON object_meta.id=object_meta_i18n_xx.object_meta_id
        AND object_meta_i18n_xx.language='de'

 WHERE FIND_IN_SET('product', object.type)
   AND FIND_IN_SET('online', object.access_status )
   AND FIND_IN_SET('architonic', object.projects_set )
   AND FIND_IN_SET('object', object.attachments_set )

 GROUP BY family.id

 ORDER BY family.id DESC;