array_cat = XQuery("sql:
	
	SELECT
		c.code,
		c.fullname,
		c.position_name,
		c.position_parent_name,
		ec.name AS ab,
		ec.start_date AS start,
		ec.finish_date AS finish,
		_cert.delivery_date AS delivery_date,
		_cert.expire_date AS expire_date
	FROM
		event_collaborators AS ec
		JOIN collaborators AS c 
			ON ec.collaborator_id=c.id 
			AND ec.is_collaborator=1 
			AND (c.position_parent_id=7102165666545098057     
			OR c.position_parent_id=6828865249108380579     
			OR c.position_parent_id=6828865249108380580
			OR c.position_parent_id=6828865249108380584     
			OR c.position_parent_id=6828865249108380587     
			OR c.position_parent_id=6828865249108380592  
			OR c.position_parent_id=6828865249108380607    
			OR c.position_parent_id=6828865249108380613
			OR c.position_parent_id=7449130307771853076
			OR c.position_parent_id=6828865249108380614)   
			AND c.is_dismiss!=1
		LEFT JOIN certificates as _cert
			ON _cert.event_id=ec.event_id 
			AND _cert.person_id=ec.collaborator_id
			AND _cert.valid=1
	WHERE
		ec.name LIKE N'СПП ОГ: «Перевозка опасных грузов воздушным транспортом» (категория 12 ИКАО)' 
		OR ec.name LIKE N'СПП ПРОФАЙЛ: «Выявление потенциально опасных пассажиров путем специального опроса пассажиров «профайлинг» в ходе предполетного обслуживания»' 
		OR ec.name LIKE N'%СПП СК:%' 
		OR ec.name LIKE N'СПП ВС: «Перронный контроль и досмотр воздушных судов»' 
		OR ec.name LIKE N'СПП КЗА: «Предотвращение несанкционированного доступа в КЗА».' 
		OR ec.name LIKE N'СПП ВЫЯВЛ: «Выявление и локализация опасных предметов и веществ»' 
	ORDER BY
		fullname
")

csv_string = EncodeCharset('код;ФИО;должность;подразделение;название программы;начало действия;окончание действия;дата начала мероприятия;дата окончания мероприятия\n', 'windows-1251')
for (elem in array_cat) {
	csv_string += EncodeCharset(StrRightRangePos(elem.code, 1) + ';' + elem.fullname + ';' + elem.position_name + ';' + elem.position_parent_name + ';' + elem.ab+ ';' + elem.delivery_date + ';' + elem.expire_date + ';' + elem.start + ';' + elem.finish +'\n', 'windows-1251')
}




path = 'E:\\toShB\\'
file_path = path+'ab2.csv'

ObtainDirectory(path, true)
PutFileData(file_path, csv_string)
