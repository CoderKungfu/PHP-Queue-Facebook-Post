<?php
# Rename this file to env.php and place in the same folder as cli.php and daemon.php
putenv("backend_target=MainQueue");
putenv("recipient_store=RecipientStore");
putenv("fb_app_id=xxxxxxxxxxxx");
putenv("fb_app_secret=xxxxxxxxxxxxxxxx");

putenv("pdo_string=mysql:host=localhost;dbname=fbposts");
putenv("db_user=root");
putenv("db_password=password");

putenv("fb_debug=1"); // 1 = true, 0 = false