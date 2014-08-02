/* New columns */
ALTER TABLE  `din_user` ADD  `source` VARCHAR( 255 ) NOT NULL DEFAULT  'inside';
ALTER TABLE  `din_user` ADD  `registration_status` VARCHAR( 255 ) NOT NULL DEFAULT  'full';
ALTER TABLE  `din_user` ADD  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE  `din_userphonenumber` ADD  `validated` INT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `din_user` ADD  `membership_trial` VARCHAR( 255 ) NOT NULL DEFAULT  '';
