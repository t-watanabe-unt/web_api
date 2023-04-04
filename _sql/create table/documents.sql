create table `documents` (
  `id` BIGINT not null
  , `document_number` VARCHAR(255) not null
  , `document_name` VARCHAR(100) not null
  , `document_mime_type` INT not null
  , `document_extension` VARCHAR(10) not null
  , `created_at` datetime default CURRENT_TIMESTAMP not null
  , `updated_at` datetime default CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP not null
  , constraint documents_PKC primary key (id)
) ;
