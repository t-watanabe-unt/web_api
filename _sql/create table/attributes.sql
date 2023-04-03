CREATE TABLE `attributes` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `document_id` BIGINT NOT NULL,
  `key` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE attributes
  ADD CONSTRAINT attributes_FK1 FOREIGN KEY (document_id) REFERENCES documents(id)
  on delete cascade
  on update cascade;

