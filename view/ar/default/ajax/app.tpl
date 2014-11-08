<a href="{$HTML_ROOT}?controller=user&action=login">Welcome</a><br/>

<div id="login_div">
	{form ajax="yes" action="?controller=user&action=login"}
	Name: <input type="text" name="user[username]" handler="password" id="user"/><br/>
	Email: <input type="password" name="user[password]" handler="password" id="password"/><br/>
	<input type="submit" value="Submit"/>
	{/form}
	<a href="#mylink" handler="link">Set Default Username & Password</a><br/>
</div>

<span id="welcome_span" style="display: none;"></span>
<br/>
<span id="logout_span" style="display:none">
	<a href="/?controller=user&action=alert_email" local="yes">Get Email Without AJAX</a>
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

<br/>
<div id="debug"></div>



<!-- Start Templates -->
{literal}
<table style="display:none">
	<tr id="mytable_tr_tpl">
		<td><b>{$name}</b></td>
		<td>{$email}</td>
	</tr>
</table>
{/literal}
<!-- End Templates -->

