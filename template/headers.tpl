<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{ADMIN_TITLE}</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="adminstyle.css" type="text/css" />
<script language="javascript" src="javascript/menuAdmin.js"></script>
<script type="text/javascript">
<!--
function manage_user(formulaire, reference, action)
{
        formulaire.ref.value = reference;
        formulaire.ope.value = action;
        if (action =='destroy')
        {
                if (confirm('{MANAGE_USER_DELETE_CONFIRM}'))
                {
                        return(true);
                }
                else
                {
                    return(false);
                }
        }
        else
        {
                return(true);
        }
}

function is_new_icao()
{
	if(form_place.icao_place.value!="other")
	{
		save_value=true;
	}
	else
	{
		save_value=false;
	}
	form_place.airfield_name.disabled = save_value;
	form_place.airfield_oaci.disabled = save_value;
	form_place.airfield_lat.disabled  = save_value;
	form_place.airfield_long.disabled = save_value;
	form_place.airfield_alt.disabled  = save_value;
}


//-->
</script>
</head>

<body{ONLOAD}>
<script type="text/javascript" src="javascript/wz_dragdrop.js"></script>
<h1>{ADMIN_TITLE}</h1><br />