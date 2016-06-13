// Title: Timestamp picker
// Description: See the demo at url
// URL: http://us.geocities.com/tspicker/
// Script featured on: http://javascriptkit.com/script/script2/timestamp.shtml
//        http://www.wsabstract.com/script/script2/timestamp.shtml
// Version: 1.0
// Date: 12-05-2001 (mm-dd-yyyy)
// Author: Denis Gritcyuk <denis@softcomplex.com>; <tspicker@yahoo.com>
// Notes: Permission given to use this script in any kind of applications if
//    header lines are left unchanged. Feel free to contact the author
//    for feature requests and/or donations

function show_calendar(str_target, str_datetime) {
	var arr_months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
		"Juillet", "Août", "Septembre_", "Octobre", "Novembre", "Decembre"];
	//var week_days = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
	var week_days = ["Di","Lu", "Ma", "Me", "Je", "Ve", "Sa"];
	var n_weekstart = 1; // day week starts from (normally 0 or 1)

	var dt_datetime = (str_datetime == null || str_datetime =="" ?  new Date() : str2dt(str_datetime));
	var dt_prev_month = new Date(dt_datetime);
	dt_prev_month.setMonth(dt_datetime.getMonth()-1);
	var dt_prev_year = new Date(dt_datetime);	
	dt_prev_year.setMonth(dt_datetime.getMonth()-12); 	
	var dt_prev_5years = new Date(dt_datetime);	
	dt_prev_5years.setMonth(dt_datetime.getMonth()-60); 	
	var dt_next_month = new Date(dt_datetime);
	dt_next_month.setMonth(dt_datetime.getMonth()+1);
	var dt_next_year = new Date(dt_datetime);
	dt_next_year.setMonth(dt_datetime.getMonth()+12);
	var dt_next_5years = new Date(dt_datetime);
	dt_next_5years.setMonth(dt_datetime.getMonth()+60);
	var dt_firstday = new Date(dt_datetime);
	dt_firstday.setDate(1);
	dt_firstday.setDate(1-(7+dt_firstday.getDay()-n_weekstart)%7);
	var dt_lastday = new Date(dt_next_month);
	dt_lastday.setDate(0);
	
	// html generation (feel free to tune it for your particular application)
	// print calendar header
	var str_buffer = new String (
		"<html>\n"+
		"<head>\n"+
		"<title>Calendar</title>\n"+
		"</head>\n"+
		"<body bgcolor=\"4682B4\" leftmargin=\"0\" topmargin=\"0\" onload=\"javascript:self.focus();\">\n"+

		"<table class=\"clsOTable\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n"+
		"<tr>"+
		"<td bgcolor=\"#4682B4\">\n"+
		"<table cellspacing=\"1\" cellpadding=\"3\" border=\"0\" width=\"100%\">\n"+
		"<tr>\n"+
		"<td>"+
		"<a href=\"javascript:window.opener.show_calendar('"+str_target+
		"', '"+dt2dtstr(dt_prev_5years)+"'+document.cal.time.value);\">"+
		"<img src=\"images/prev.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"-5 ans\"></a>\n"+				
		"</td>"+	
		"<td bgcolor=\"#4682B4\"><a href=\"javascript:window.opener.show_calendar('"+
		str_target+"', '"+ dt2dtstr(dt_prev_year)+"'+document.cal.time.value);\">"+
		"<img src=\"images/prev.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"-1 an\"></a></td>\n"+
		"	<td bgcolor=\"#4682B4\" align=\"center\" colspan=\"3\">"+
		"<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"+
		dt_datetime.getFullYear()+"</font></td>\n"+
		"	<td bgcolor=\"#4682B4\" align=\"right\"><a href=\"javascript:window.opener.show_calendar('"
		+str_target+"', '"+dt2dtstr(dt_next_year)+"'+document.cal.time.value);\">"+
		"<img src=\"images/next.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"+1 an\"></a></td>\n"+
		"<td align=\"right\">"+
		"<a href=\"javascript:window.opener.show_calendar('"+str_target+
		"', '"+dt2dtstr(dt_next_5years)+"'+document.cal.time.value);\">"+
		"<img src=\"images/next.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"+5 ans\"></a>\n"+				
		"</td>"+	
		"</tr>\n"
	);

	var dt_current_day = new Date(dt_firstday);
	// print weekdays titles
	str_buffer += "<tr>\n";
	for (var n=0; n<7; n++)
		str_buffer += "	<td bgcolor=\"#87CEFA\">"+
		"<font color=\"white\" face=\"tahoma, verdana\" size=\"2\">"+
		week_days[(n_weekstart+n)%7]+"</font></td>\n";
	// print calendar table
	str_buffer += "</tr>\n";
	while (dt_current_day.getMonth() == dt_datetime.getMonth() ||
		dt_current_day.getMonth() == dt_firstday.getMonth()) {
		// print row heder
		str_buffer += "<tr>\n";
		for (var n_current_wday=0; n_current_wday<7; n_current_wday++) {
				if (dt_current_day.getDate() == dt_datetime.getDate() &&
					dt_current_day.getMonth() == dt_datetime.getMonth())
					// print current date
					str_buffer += "	<td bgcolor=\"#FFB6C1\" align=\"right\">";
				else if (dt_current_day.getDay() == 0 || dt_current_day.getDay() == 6)
					// weekend days
					str_buffer += "	<td bgcolor=\"#DBEAF5\" align=\"right\">";
				else
					// print working days of current month
					str_buffer += "	<td bgcolor=\"white\" align=\"right\">";

				if (dt_current_day.getMonth() == dt_datetime.getMonth())
					// print days of current month
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<font color=\"black\" face=\"tahoma, verdana\" size=\"2\">";
				else 
					// print days of other months
					str_buffer += "<a href=\"javascript:window.opener."+str_target+
					".value='"+dt2dtstr(dt_current_day)+"'+document.cal.time.value; window.close();\">"+
					"<font color=\"gray\" face=\"tahoma, verdana\" size=\"2\">";
				str_buffer += dt_current_day.getDate()+"</font></a></td>\n";
				dt_current_day.setDate(dt_current_day.getDate()+1);
		}
		// print row footer
		str_buffer += "</tr>\n";
	}
	// print calendar footer
	str_buffer +=
		"<form name=\"cal\">\n<tr>"+
		"<td bgcolor=\"#4682B4\"><a href=\"javascript:window.opener.show_calendar('"+
		str_target+"', '"+ dt2dtstr(dt_prev_month)+"'+document.cal.time.value);\">"+
		"<img src=\"images/prev.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"-1 mois\"></a></td>\n"+		
		
		"<td colspan=\"5\" align=\"center\" >"+
		"<font color=\"White\" face=\"tahoma, verdana\" size=\"2\">"+
//		"Time: <input type=\"text\" name=\"time\" value=\"xxx\" size=\"8\" maxlength=\"8\"></font></td></tr>\n</form>\n" +
		arr_months[dt_datetime.getMonth()]+
		"<input type=\"hidden\" name=\"time\" value=\"\"> </font></td>"+

//    "<td>-</td>"+
		"<td bgcolor=\"#4682B4\" align=\"right\"><a href=\"javascript:window.opener.show_calendar('"
		+str_target+"', '"+dt2dtstr(dt_next_month)+"'+document.cal.time.value);\">"+
		"<img src=\"images/next.gif\" width=\"16\" height=\"16\" border=\"0\""+
		" alt=\"+1 mois\"></a></td>\n</tr>\n"		
		
		"</tr>\n</form>\n" +
		"</table>\n" +
		"</tr>\n</td>\n</table>\n" +
		"</body>\n" +
		"</html>\n";		
		
	var vWinCal = window.open("", "Calendar", 
		"width=200,height=213,status=no,resizable=yes,margin=0,top=200,left=200");
	vWinCal.opener = self;
	var calc_doc = vWinCal.document;
	calc_doc.write (str_buffer);
	calc_doc.close();   
}
// datetime parsing and formatting routimes. modify them if you wish other datetime format
function str2dt (str_datetime) {
	//var re_date = /^(\d+)\-(\d+)\-(\d+)\s+(\d+)\:(\d+)\:(\d+)$/;
//	var re_date = /^(\d+)\-(\d+)\-(\d+)$/;
	var re_date = /^(\d+)\/(\d+)\/(\d+)$/;
	if (!re_date.exec(str_datetime))
		return alert("Format de date invalide: "+ str_datetime);
	return (new Date (RegExp.$3, RegExp.$2-1, RegExp.$1, RegExp.$4, RegExp.$5, RegExp.$6));
}
function dt2dtstr (dt_datetime) {
	return (new String (
			dt_datetime.getDate()+"/"+(dt_datetime.getMonth()+1)+"/"+dt_datetime.getFullYear()+""));
}
function dt2tmstr (dt_datetime) {
	return (new String (
			dt_datetime.getHours()+":"+dt_datetime.getMinutes()+":"+dt_datetime.getSeconds()));
}
