create table `attributes` (
  `id` INT(11) not null
  , `document_id` BIGINT not null
  , `key` VARCHAR(10) not null
  , `value` VARCHAR(20) not null
  , `created_at` DATETIME default CURRENT_TIMESTAMP not null
  , `updated_at` DATETIME default CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP not null
  , constraint attributes_PKC primary key (id)
) ;

ALTER TABLE `attributes`
  ADD CONSTRAINT attributes_FK1 FOREIGN KEY (document_id) REFERENCES documents(id)
  on delete cascade
  on update cascade;

