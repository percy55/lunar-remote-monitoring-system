L.U.N.A.R. - Lightning-fast Unlimited/Unique Number Analyser and Responder
v0.91
By Martin A. COLEMAN (C) 2013.

APPROXIMATELY 80% COMPLETE ONLY.

LUNAR provides a remote monitoring, analysis and management portal with a web-based facility designed for monitoring of electronic equipment with network capability, such as some inverters for solar systems.

Now, on to specifics. Any modest VPS can handle up to approximately 4,000 customers/users at once, there is no practical limit on the number of records you can hold (subject to storage space available) and the parsing engine is really, really fast.

Code Structure:
  data/ - This contains all the SQL to create the databases.
  receiver/ - This contains the code that accepts the remote data requests and records equipment readings.
  sender/ - This contains a mock inverter "simulator".
  website/ - This is all the website data, for users, installers/resellers (soon!) and the equipment manufacturer.
  system/ - This is where all the data processors go.
  test/ - A folder for experimental code. This is to test ideas and new features.
  
The Component Files:
*.SQL - Intended for SQLite3. These keep track of users, equipment (such as inverters), equipment readings, installers/resellers and solar installs.
record.* - A C and a PHP file that do exactly the same thing are provided. The PHP was for proof of concept, the C file was to do what the PHP file does, just a lot faster. This enables you to facilitate an even greater number of users.
inverter.php - It sends a reading of random data to the DB recorder. It's basically just to show how to send the data in the simplest and most straight forward way.
website/* - Should be self explanatory.
calc_kwh.c - I recommend a crontab job every 10 minutes for this. It updates the kWh number on the main login page and the control panel. Mostly for boasting purposes to your customers and installers, but handy to know for the manufacturer as well.
canary.c - The digital "miner's canary". Read the comments in the file, this oversees and monitors everything in case of a fault or potential problem. Still in heavy development. I recommend running this every 5 minutes.
view.php - Soon to be converted to C. This creates the equipment performance graph. This is under heavy development.

Acknowledgments:
I wrote this entirely from scratch while reading a project scope for a web-based monitoring facility for an electronic equipment manufacturer, with two exceptions:
* Special thanks to the SQLite project for SQLite3 (www.sqlite.org). Wonderful database people!
* Special thanks to the qDecoder project (www.qdecoder.org/wiki/qdecoder). Very useful CGI library for C!

License:
Released under the BSD 2-Clause license. Don't want to have to include the license text when you commercially implement this? Talk to me.

Sign Off:
Martin A. COLEMAN, Caloundra, Australia. May 2013.
http://www.martincoleman.com/lunar.html
