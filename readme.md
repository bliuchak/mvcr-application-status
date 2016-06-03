mvcr-application-status
=============

This application was made for self-purposes to automate checking of `.xls` document and try `Symphony Console` component.

This is simple command-line application based on `Nette` and `Symphony Console` component which helps to find specific `case-number` in `.xls` document. In other words check status of your application.

See page of [mvcr.cz](http://www.mvcr.cz/mvcren/article/status-of-your-application.aspx) for more details.

**Note:** script handle only sheet `Zaměstnanecká karta`.

How to use
------------

1. Clone this repository and run `composer update`
2. Use command `sudo php www/index.php papers:check [document-number]` to run the application. You can pass full number `ABC-99999/XY-9999` or some part `ABC-99999` or even `99999`
3. Possible outputs:
	4. Positive - `Possible matches: ABC-99999/XY-9999`
	5. Negative - `Empty result`

Components
------------
1. Nette framework
2. Symphony Console
3. PHPExcel
