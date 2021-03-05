/*use this file to rebuild your database.*/
/*
keep the following 2 lines commented out the first time
then uncomment them to wipe and reset the db
*/
/*create database*/
mysql -h wwwstu.csci.viu.ca -p
create database csci311a_project;

/*using database*/
cd public_html/project/private/
mysql -h wwwstu.csci.viu.ca -p
u682uo09
use csci311a_project;

/*drop table*/
drop table Account;
drop table Books;
drop table General_Books;
drop table Text_Books;
drop table Trad_Books;
/*create 5 tables*/

Create table members(
	username varchar(40) not null,
	password varchar(256) not null,
	email varchar(40) not null
);

Create table Account(
	Account_ID int not null auto_increment primary key,
	userid varchar(40) not null,
	password varchar(256) not null,
	UserName varchar(40) not null, 
	email varchar(40) not null
);
Create table Books( 
	Book_ID int not null auto_increment primary key, 
	Title varchar(70) not null,
	Author varchar(70) not null,
	Publisher varchar(70),
	ISBN Bigint,
	Contact varchar(70) not null,
	Price DOUBLE(8, 2) not null,
	Photo varchar(70) not null,
	Type varchar(4) not null,
	Post_Time date not null,
	Sold_Time date, 
	Account_ID int not null
);
Create table General_Books(
	Categories varchar(20) not null,
	Book_ID int not null
);
Create table Text_Books(
	Program varchar(20) not null,
	Course varchar(4) not null,
	CourseNum int not null,
	Instructor varchar(20) not null,
	Book_ID int not null
);
Create table Trad_Books(
	TB_ID int not null auto_increment primary key, 
	TB_Title varchar(70) not null,
	TB_Author varchar(70) not null,
	TB_Publisher varchar(70),
	TB_ISBN Bigint,
	TB_Photo varchar(70) not null,
	Book_ID int not null
);
/*insert data using csv file*/
LOAD DATA LOCAL INFILE './Account.csv'
INTO TABLE Account
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './Books.csv'
INTO TABLE Books
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './General_Books.csv'
INTO TABLE General_Books
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './Text_Books.csv'
INTO TABLE Text_Books
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './Trad_Books.csv'
INTO TABLE Trad_Books
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

/*show data*/
select * from Account;
select * from Books;
select * from General_Books;
select * from Text_Books;
select * from Trad_Books;

/*updte data*/
update Books set Sold_Time = '0000-00-00' where Account_ID = 1;
update General_Books set Book_ID = 17 where Book_ID = 18;

delete from General_Books where Categories = "Anatomy";
delete from General_Books where Book_ID = 19;
/*Home new imgs*/
select Photo, Title from Books 
where Type = 'Text' and Sold_Time = '0000-00-00'
order by Post_Time desc;

select Photo, Title from Books 
where Type = 'Book' and Sold_Time = '0000-00-00'
order by Post_Time desc;

/*Search books page*/
select Book_ID
from General_Books
where Categories like :Categories;


select Photo, Title, Author, Publisher, Price, UserName, Contact
from Books join Account on Books.Account_ID = Account.Account_ID
where sold_Time = '0000-00-00'
and Title like :Title or Author like :Author or Publisher like :Publisher;

select Photo, Title, Author, Publisher, Price, UserName, Contact from Books join Account on Books.Account_ID = Account.Account_ID
where sold_Time = '0000-00-00' and Books.Book_ID IN (select Books.Book_ID from Books join Text_Books on Text_Books.Book_ID = Books.Book_ID 
where Program = :Program or Course like :Course or Instructor like :Instructor);

select Photo, Title, Author, Publisher, Price, UserName, Contact from Books join Account on Books.Account_ID = Account.Account_ID
where sold_Time = '0000-00-00' and Books.Book_ID IN (select Books.Book_ID from Books join General_Books on General_Books.Book_ID = Books.Book_ID 
where Categories = :Categories or Title like :Title);

select Photo, Title, Author, Publisher, Price, UserName, Contact from Books join Account on Books.Account_ID = Account.Account_ID
where sold_Time = '0000-00-00' and Books.Book_ID IN (select Books.Book_ID from Books join General_Books on General_Books.Book_ID = Books.Book_ID 
where Categories = "" or Title like "H%");

/*Trade books page - Show all*/
select Photo, Title, Author, Publisher, Price, UserName, Contact, TB_Photo, TB_Title, TB_Author, TB_Publisher
from Books, Account, Trad_Books
where Books.Book_ID = Trad_Books.Book_ID
	and Books.Account_ID = Account.Account_ID
	and sold_Time = '0000-00-00'
order by Post_Time desc;


/*account profile search*/
select Photo, Title, Author, Publisher, Price, UserName, Contact
from Books join Account on Books.Account_ID = Account.Account_ID
where sold_Time = '0000-00-00' and Books.Account_ID = 5;


select Photo, Title, Author, Publisher, Price, UserName, Contact
from Books join Account on Books.Account_ID = Account.Account_ID
where Sold_Time = '0000-00-00' order by Post_Time desc;

/*add books*/
select MAX(Book_ID) from Books;

insert into Books(Title, Author, Publisher, ISBN, Contact, Price, Photo, Type, Post_Time, Sold_Time, Account_ID)
values (:Title, :Author, :Publisher, :ISBN, :Contact, :Price, :Photo, :Type, :Post_Time, '0000-00-00', :Account_ID);

select MAX(Books.Book_ID)
from Books join Account on Books.Account_ID = Account.Account_ID
where Books.Account_ID = 20;

insert into Text_Books(Program, Course, CourseNum, Instructor, Book_ID)
values (:Program, :Course, :CourseNum, :Instructor, :Book_ID);

insert into General_Books(Categories, Book_ID)
values (:Categories, :Book_ID);

insert into Trad_Books(TB_Title, TB_Author, TB_Publisher, TB_ISBN, TB_Photo, Book_ID)
values (:TB_Title, :TB_Author, :TB_Publisher, :TB_ISBN, :TB_Photo, :Book_ID);

















	
