<a href="{$HTML_ROOT}?controller=user&action=login">Welcome</a><br/>

<div id="login_div">
	{form ajax="yes" action="?controller=user&action=login"}
	Email: <input type="text" name="user[email]" handler="password" id="email"/><br/>
	Password: <input type="password" name="user[password]" handler="password" id="password"/><br/>
	<input type="submit" value="  Login  "/>
	{/form}
	<a href="#mylink" handler="link">Set Default Email & Password</a><br/>
</div>

<span id="welcome_span" style="display: none;"></span>
<br/>
<span id="logout_span" style="display:none">
	<a href="/?controller=user&action=alert_name" local="yes">Get Name Without AJAX</a>
	<br/>
	<a href="{$HTML_ROOT}?controller=user&action=logout" ajax="yes">Log out</a>
</span>

<br/>
<b>Table</b><br/>

<table border="1" id="mytable">
	<tr>
		<td><b>Name</b></td>
		<td><b>Email</b></td>
	</tr>
</table>

- <a href="#" handler="row_adder" index="0" name="Ahmed" email="ahmed@domain.com">Index 0</a><br/>
- <a href="#" handler="row_adder" index="1" name="Magdy" email="magdy@domain.com">Adder 1</a><br/>
- <a href="#" handler="row_adder" index="-1" name="Mohamed" email="mohamed@domain.com">Adder -1</a><br/>
- <a href="#" handler="row_adder" name="Ezzeldin" email="ezzeldin@domain.com">Adder At End</a><br/>

<br/><br/>
- <a href="{$HTML_ROOT}?controller=ajax&action=test_slow" ajax="yes" queue="yes">Slow</a><br/>
- <a href="{$HTML_ROOT}?controller=ajax&action=test_fast" ajax="yes" queue="yes">Fast</a><br/>
<br/>
<br/>




<!-- Start Templates -->
{literal}
<table style="display:none">
	<tr id="mytable_tr_tpl">
		<td bgcolor="#FF0000"><b>{$name}</b></td>
		<td>{$email}</td>
	</tr>
</table>
{/literal}
<!-- End Templates -->

Address:
<select name="user[country_id]" id="country_id">
        <option>--N/A--</option>
	<option value="EG">Egypt</option>
	<option value="US">USA</option>
	<option value="UK">UK</option>
	<option value="FR">France</option>
</select>
{ajax_select hide_if_empty=true empty_entry_text="-- None --" controller="ajax" action="get_cities" name="user[city_id]" id="city_id" changer_id="country_id" style="display:none"}
{ajax_select hide_if_empty=true empty_entry_text="-- None --" controller="ajax" action="get_areas" name="user[area_id]" id="area_id" changer_id="city_id" style="display:none"}

<br/><br/><br/>
<textarea id="debug" cols="90" rows="15"></textarea>
