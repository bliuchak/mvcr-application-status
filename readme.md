mvcr-application-status
=============

This application was made for self-purposes to automate checking of `.xls` document and try `Symphony Console` component.

This is simple command-line application based on `Nette` and `Symphony Console` component which helps to find specific `case-number` in `.xls` document. In other words check status of your application.

See page of [mvcr.cz](http://www.mvcr.cz/mvcren/article/status-of-your-application.aspx) for more details.

By default script will search in **all sheets** and handle **all** years. To set more specific filters please use the following options `--year` and `--type`.

How to use
------------

1. Clone this repository and run `composer update`
2. Use command `sudo php www/index.php papers:check [--type=ALL] [--year=ALL] [document-number]`

If you have number `XXX-09999-9/ZZ-2015` your command should look like (use numbers between `XXX-` and `-9` without `0` at the beginning):
`sudo php www/index.php papers:check 9999`

Command-line options
------------
1. `--type` by default equal to `all`. List of possible types: `DV`, `PP`, `DP`, `ZM` and `TP`
Example `sudo php www/index.php papers:check 99999 --type TP`
2. `--year`by default equal to `all`. Script will try find number in all available years.
Example `sudo php www/index.php papers:check 99999 --year 2016`


Components
------------
1. Nette framework
2. Symphony Console
3. PHPExcel
4. Semantic UI
