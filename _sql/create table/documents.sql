create table `documents` (
  `id` BIGINT not null AUTO_INCREMENT
  , `document_number` VARCHAR(255) not null
  , `document_name` VARCHAR(100) not null
  , `document_mime_type` VARCHAR(100) not null
  , `document_extension` VARCHAR(10) not null
  , `created_at` datetime default CURRENT_TIMESTAMP not null
  , `updated_at` datetime default CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP not null
  , constraint documents_PKC primary key (id)
) ;
