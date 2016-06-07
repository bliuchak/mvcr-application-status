mvcr-application-status
=============

This application was made for self-purposes to automate checking of `.xls` document and try `Symphony Console` component.

This is simple command-line application based on `Nette` and `Symphony Console` component which helps to find specific `case-number` in `.xls` document. In other words check status of your application.

See page of [mvcr.cz](http://www.mvcr.cz/mvcren/article/status-of-your-application.aspx) for more details.

By default script will search in **all sheets** and handle **all** years. To set more specific filters please use the following options `--year` and `--section`.

How to use
------------

1. Clone this repository and run `composer update`
2. Use command `sudo php www/index.php papers:check [--section=ALL] [--year=ALL] [document-number]`
3. Possible outputs:
	4. Positive - `Possible matches: ABC-99999/XY-9999`
	5. Negative - `Empty result`

If you have number `XXX-09999-9/ZZ-2015` your command should look like (use numbers between `XXX-` and `-9` without `0` at the beginning):
`sudo php www/index.php papers:check 9999`

Command-line options
------------
1. `--section` by default equal to `all`. Script will try to find number in all document sections. There are 3 available sections: 
	- `lt` for `DP, PP, DV - prodl.`
	- `ec` for `Zaměstnanecká karta`
	- `pt` for `Trvalé pobyty`
Example `sudo php www/index.php papers:check --section=lt 99999`
2. `--year`by default equal to `all`. Script will try find number in all available years.
Example `sudo php www/index.php papers:check --year=2015 99999`


Components
------------
1. Nette framework
2. Symphony Console
3. PHPExcel
