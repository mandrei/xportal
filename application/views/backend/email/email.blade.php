<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Email</title>

	</head>

	<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Helvetica, Arial, sans-serif; font-size:14px;">

		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="line-height:1.2em;">

			<tr>

				<td valign="top" style='vertical-align:top' align="left">

					<table border="0" cellpadding="0" cellspacing="0" height="100%" width="600" style="width:600px; height:100%;">

						<tr>

							<td valign="top" style='vertical-align:top' align="left">


								<table border="0" cellpadding="0" cellspacing="0" width="600" style="width:600px; font-size:1em;">

									<tr>	

										<td valign="top" style='vertical-align:top; text-align:center' align="center" style="text-align:center;">

											<table border="0" cellpadding="0" cellspacing="0" align="center" width="550" style="width:550px; margin:auto;">

												<tr>

													<td valign="top" style='vertical-align:top;  text-align:left' align="left">

														<br />

													<?php echo $title; ?>

														<br />													


													</td>

												</tr>

											</table>

											<table border="0" cellpadding="0" cellspacing="0" align="center" width="550" style="width:550px; margin:auto;">

												<tr>

													<td style='height:1em;'></td>

												</tr>

											</table>

											<table border="0" cellpadding="0" cellspacing="0" align="center" width="550" style="width:550px; margin:auto;">

												<tr>

													<td valign="top" style='vertical-align:top; text-align:left' align="left">

														<br />

                                                       <?php echo $message; ?>

                                                        <br /><br /><br />

                                                       Click <a href="{{URL::base()}}/login" target="_blank">here</a> to access the portal.

													</td>

												</tr>

											</table>

										</td>

									</tr>

								</table>

								<table border="0" cellpadding="0" cellspacing="0" width="600" height="70" style="width:600px; font-size:11px;margin:auto; font-family: Calibri sans-serif; margin:25px;">



									<tr>
										
										<td>
											
											<img src="{{URL::base()}}/img/email/email.png">

											<br/>

											
										</td>

									</tr>

								</table>

							</td>

						</tr>

					</table>

				</td>

			</tr>

		</table>

	</body>

</html>