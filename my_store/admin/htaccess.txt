RewriteEngine On
RewriteRule ^$ frontend/index.php [L]
RewriteRule ^((?!frontend/|admin/|image/).*)$ frontend/$1 [L]