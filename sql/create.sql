create table maildb ( 
	mail_id serial,
	to_address varchar(100),
	subject varchar(100),
	one_comment varchar(100),
	create_date datetime,
	date1 date,
	date2 date,
	primary key(mail_id) 
)