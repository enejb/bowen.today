This is a basic next ferry implementation in PHP

Version 0.1 Works for Bowen Island

To run the tests:
`phpunit lib/next-ferry/testcase.php`


To start the server for local dev
`php -S localhost:8000`


Todo
[ ] Provide a more human readable time.
[ ] Make it work with timezones. (Test that part out)
[ ] Make it work for the other direction. (Vancouver to Bowen)
[ ] Create a new Google Assistant Skill api.
[ ] Try to use more live data instead the schedule. Need to figure out what data to scrape.
[ ] Allow a different time then just now to be set. (When do you want to leave? )
[ ] Optimization cache the wrappoing results locally for faster schedule.
[ ] Support more routes. ???
[ ] create an api endpoint that returns the whole timetable in a computer readable format. 



// 

api returns 
to: 
from:
map_url:
next_ferry: timestamp
next_sailings: [timestamp]
current_capacity: %
has_current_capacity: available (bool)