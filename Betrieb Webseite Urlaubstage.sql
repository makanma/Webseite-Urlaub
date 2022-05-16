create table maxurlaubstage (
	id serial primary key,
	fk_maxurlaubstage_mitarbeiter int,
	urlaubstage int default 20,
	constraint fk_maxurlaubstage
		foreign key(fk_maxurlaubstage_mitarbeiter)
			references mitarbeiter(id_mitarbeiter)
);

create table urlaubgenommen(
	id serial primary key,
	username text,
	beginning date,
	endtime date,
	status text
);

CREATE TABLE users (
    id serial PRIMARY KEY,
    fk_mitarbeiter int,
    username text NOT NULL UNIQUE,
    password text NOT NULL,
    userrole text default 'employee',
    created_at date DEFAULT CURRENT_TIMESTAMP
);



insert into users (fk_mitarbeiter,username,"password",userrole) values (1,'admin',(SELECT MD5('admin')),'admin');

create table urlaub_Antrag(
	id serial primary key,
	username text,
	urlaub_start date,
	urlaub_end date,
	status text,
	created_at date DEFAULT CURRENT_TIMESTAMP

)





select generateUrlaub();

-- generate table records in maxurlaubstage
create or replace function generateUrlaub()
	returns void
	LANGUAGE plpgsql
as $$
declare
	datum1 date;
	rec int;
begin
	
	FOR rec IN
    	SELECT id_mitarbeiter from mitarbeiter
	loop
		if(rec not in (select fk_maxurlaubstage_mitarbeiter from maxurlaubstage)) then
    		INSERT INTO maxurlaubstage (fk_maxurlaubstage_mitarbeiter) VALUES (rec);
    	end if;
	END LOOP;
	
end;
$$;

select count(id) from users where username = 'admin' and "password" = (SELECT MD5('admin'));


select checkLoginData('admin','admin');
select checkLoginData('admin','21232f297a57a5a743894a0e4a801fc3');

select userrole from users u where u.username = 'admin' and u.passw = '21232f297a57a5a743894a0e4a801fc3';

create or replace function checkLoginData(col_username text, col_pass text)
	returns bool
	LANGUAGE plpgsql
as $$
declare
	c int;
begin
	
	select count(id) into c from users u where u.username = col_username and u.passw = col_pass;
	
	if(c=1) then
	 	return 't';
	end if;
	return 'f';
	
end;
$$;

create or replace function getUserRole(col_username text, col_pass text)
	returns text
	LANGUAGE plpgsql
as $$
declare
	uRole text;
begin
	select userrole into uRole from users u where u.username = col_username and u.passw = col_pass;
	return uRole;
end;
$$;

select * from mitarbeiter m join users u on  m.id_mitarbeiter = u.fk_mitarbeiter ;
select getCreatedUsers();


create or replace function getCreatedUsers()
	returns table(id int)
	LANGUAGE plpgsql
as $$
declare
	c int;
begin
	return query
		select id_mitarbeiter from mitarbeiter m join users u on  m.id_mitarbeiter = u.fk_mitarbeiter;
	
end;
$$;

select m.id_mitarbeiter from users u 
inner join mitarbeiter m on m.id_mitarbeiter = u.fk_mitarbeiter ;

select createUser('10','asd','pass','test');

create or replace function createUser(id_employee text, username_input text, passw_val text, userrole_value text)
	returns bool
	LANGUAGE plpgsql
as $$
declare
	id_table int;
begin
	insert into users (fk_mitarbeiter, username,passw,userrole) values (id_employee::int,username_input,passw_val,userrole_value);
	return true;
	EXCEPTION WHEN OTHERS THEN
  		return false;
  	
  	
end;
$$;

create or replace function resetPassworUser(id_employee text, newPassword text)
	returns void
	LANGUAGE plpgsql
as $$
declare

begin
	update users set passw = newPassword where fk_mitarbeiter = id_employee::int;
end;
$$;

create or replace function changeRoleUser(id_employee text, newRole text)
	returns void
	LANGUAGE plpgsql
as $$
declare

begin
	update users set userrole = newRole where fk_mitarbeiter = id_employee::int;
end;
$$;

--select deleteUser('95', 'admin');

create or replace function deleteUser(id_employee text,currentUser_Username text)
	returns void
	LANGUAGE plpgsql
as $$
declare
	uName text;
begin
	select username into uName from users where fk_mitarbeiter = id_employee::int;
	if(uName != currentUser_Username) then
		delete from users where fk_mitarbeiter = id_employee::int;
	end if;
end;
$$;



create or replace function setHolidayRequest(input_username text,startDate text, endDate text, days text)
	returns void
	LANGUAGE plpgsql
as $$
declare
begin
	insert into urlaub_Antrag (username,urlaub_start,urlaub_end,status,urlaubstage) values (input_username,TO_DATE(startDate,'YYYY-MM-DD'),TO_DATE(endDate,'YYYY-MM-DD'),'pending',days::int);
end;
$$;

drop function getmaxHolidays;

select getAvailableHolidays('test1');
select urlaubstage from maxurlaubstage m inner join users u on m.fk_maxurlaubstage_mitarbeiter = u.fk_mitarbeiter where u.username='test1';
select sum( case when status='pending' or status='approved' then urlaub_antrag.urlaubstage else 0 end ) from urlaub_antrag where username='test1' and date_part('year', CURRENT_DATE) =  date_part('year', urlaub_start) and date_part('year', CURRENT_DATE) = date_part('year', urlaub_end) ;
select date_part('year', CURRENT_DATE);
select date_part('year', urlaub_start) from urlaub_antrag ua where id=62;

create or replace function getAvailableHolidays(input_username text)
	returns text
	LANGUAGE plpgsql
as $$
declare
	maxHolidays text;
	sum_int int;
begin
	select urlaubstage into maxHolidays from maxurlaubstage m inner join users u on m.fk_maxurlaubstage_mitarbeiter = u.fk_mitarbeiter where u.username=input_username;
	select sum( case when status='pending' or status='approved' then urlaub_antrag.urlaubstage else 0 end ) into sum_int from urlaub_antrag where username=input_username and date_part('year', CURRENT_DATE) =  date_part('year', urlaub_start) and date_part('year', CURRENT_DATE) = date_part('year', urlaub_end);
	select COALESCE (sum_int,0) into sum_int;
	return (maxHolidays::int - sum_int);
end;
$$;
select * from getAllUserRequests('admin');


create or replace function getAllUserRequests(input_username text)
	returns table(uid int,uStart text, uEnd text, uStatus text)
	LANGUAGE plpgsql
as $$
declare
begin
	return query
		select id, urlaub_start::text, urlaub_end::text, status from urlaub_antrag where username = input_username order by id;
	
end;
$$;

create or replace function getLastHolidayRequest(input_username text)
	returns table(uid int,uStart text, uEnd text, uStatus text)
	LANGUAGE plpgsql
as $$
declare
begin
	return query
		select id, urlaub_start::text, urlaub_end::text, status from urlaub_antrag where username = input_username order by id desc limit 1;
	
end;
$$;

select deleteHolidayRequest('admin', '37');

create or replace function deleteHolidayRequest(input_username text, idHolidayRequest text)
	returns int
	LANGUAGE plpgsql
as $$
declare
 	st text;
	tage int := 0;
begin
	raise notice 'begin: %',tage;
	select status into st from urlaub_antrag where urlaub_antrag.id = idHolidayRequest::int;
	if(st='pending') then 
		select urlaubstage::int into tage::int from urlaub_antrag where urlaub_antrag.id = idHolidayRequest::int and username = input_username;
		delete from urlaub_antrag where urlaub_antrag.id = idHolidayRequest::int and username = input_username;
	end if;
	
	return tage;
end;
$$;

select * from mitarbeiter
inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
inner join urlaub_antrag ua on u.username = ua.username 



(select vorname,nachname,m.id_mitarbeiter,ua.id as urlaub_id,urlaub_start,urlaub_end,status,urlaubstage from mitarbeiter m
inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
inner join urlaub_antrag ua on u.username = ua.username) into test;



-- do I need this?
select * from getAllHolidayRequests();
create or replace function getAllHolidayRequests()
	returns table(vorname text,nachname text, id_mitarbeiter int,urlaub_id int, urlalaub_start date, urlaub_end date, status text,urlaubstage int)
	LANGUAGE plpgsql
as $$
declare
 	st text;
	tage int := 0;
begin
	return query
		select m.vorname,m.nachname,m.id_mitarbeiter,ua.id as urlaub_id, ua.urlaub_start, ua.urlaub_end, ua.status, ua.urlaubstage from mitarbeiter m
		inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
		inner join urlaub_antrag ua on u.username = ua.username;
		
	
end;
$$;




select getPendingCountAll();
create or replace function getPendingCountAll()
	returns int
	LANGUAGE plpgsql
as $$
declare
	c int := 0;
begin
	select count(case when status='pending' then urlaub_antrag.status end) into c from urlaub_antrag;
	return c;
end;
$$;


select * from getRequestCountPerUser();

create or replace function getRequestCountPerUser()
	returns table(vorname text,nachname text, id int, c int)
	LANGUAGE plpgsql
as $$
declare
begin
	return query
		select a.vorname,a.nachname, a.id_mitarbeiter ,count(a.vorname)::int  from (select m.vorname,m.nachname,m.id_mitarbeiter,ua.id as urlaub_id, ua.urlaub_start, ua.urlaub_end, ua.status, ua.urlaubstage from mitarbeiter m
			inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
			inner join urlaub_antrag ua on u.username = ua.username) a  where status='pending' group by a.id_mitarbeiter ,a.vorname,a.nachname order by a.vorname,a.nachname;
	
	
end;
$$;


select * from getHolidayRequestsOfUser('1');
create or replace function getHolidayRequestsOfUser(userid text)
	returns table(urlaub_id int, urlalaub_start date, urlaub_end date, status text,urlaubstage int)
	LANGUAGE plpgsql
as $$
declare
 	st text;
	
begin
	return query
		select ua.id as urlaub_id, ua.urlaub_start, ua.urlaub_end, ua.status, ua.urlaubstage from mitarbeiter m
		inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
		inner join urlaub_antrag ua on u.username = ua.username 
		where m.id_mitarbeiter=userid::int order by ua.urlaub_start;
end;
$$;


create or replace function holidayStatusChange(holidayid text,changeAction text)
	returns void
	LANGUAGE plpgsql
as $$
declare
begin
	update urlaub_antrag set status=changeAction where id=holidayid::int;
end;
$$;



select * from currentPeopleHoliday();

create or replace function currentPeopleHoliday()
	returns table(vorname text, nachname text, uStart date, uEnd date, status text)
	LANGUAGE plpgsql
as $$
declare
begin
	return query
	select m.vorname,m.nachname, ua.urlaub_start, ua.urlaub_end, ua.status from mitarbeiter m
		inner join users u on u.fk_mitarbeiter = m.id_mitarbeiter 
		inner join urlaub_antrag ua on u.username = ua.username
		where now()::date between ua.urlaub_start and ua.urlaub_end;
end;
$$;
