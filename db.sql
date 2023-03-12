CREATE TABLE `payments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `payment_system` varchar(10) NOT NULL,
    `operation_id` varchar(25) NOT NULL,
    `amount` float(10,2) NOT NULL,
    `title` varchar(50) NOT NULL,
    `datetime` DATETIME NOT NULL,
    `_user_id` int(11) NULL,
    `_order_id` varchar(20) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
