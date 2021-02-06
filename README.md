## Repo behind the website [bowen.today](http://bowen.today)

Thanks for stopping by.

### Contents

/lib/next-ferry - library to get back ferry data. Ferry API

/lib/pages - Simple pages

/lib/simplehtmldom - [PHP Simple HTML DOM Parser](https://simplehtmldom.sourceforge.io/)

#### [API Returns](http://bowen.today/api/v1/ferry)

- `to:`
- `from:`
- `map_url:`
- `next_ferry: timestamp`
- `next_sailings: [timestamp]`
- `current_capacity: %`
- `has_current_capacity: available (bool)`


http://bowen.today/api/v1/ferry/vancouver-bowen
http://bowen.today/api/v1/ferry/bowen-vancouver

You can also pass in a specific future date to get the schedule from the future.
http://bowen.today/api/v1/ferry/bowen-vancouver/2021-02-22

### Local development.

To start the server for local development.

```
cd public
php -S localhost:8000
```

then visit
[localhost:8000](http://localhost:8000/)

### Sass Basics.

The watch flag tells Sass to watch style.scss, and re-compile CSS each time you save your Sass.

Open this in a separete terminal window.

```
cd public
sass --watch style.scss style.css
```

## Todo Version 0.1

- [ ] Create a new Google Assistant Skill api.
- [ ] Styles and Design/UI/UX
- [ ] Add SASS support
  - [ ] Create SASS structure for vars, mixins, etc
- [ ] Real time vessel position APIs
  - [ ] [Vessel Finder premium API](https://api.vesselfinder.com/docs/)
  - [ ] [BC Ferries 2014 Python API](http://yasyf.github.io/bcferries/)
  - [ ] [Canada Data Catalogue](https://catalogue.data.gov.bc.ca/dataset?tags=route)
- [ ] Implement separtion for Departures Today/Next Day `./ferry/index.php`
- [x] ~~Provide a more human readable time.~~
- [x] ~~Make it work with timezones. (Test that part out)~~
- [x] ~~Make it work for the other direction. (Vancouver to Bowen)~~
- [x] ~~Try to use more live data instead the schedule. Need to figure out what data to scrape.~~
- [x] ~~Allow a different time then just now to be set. (When do you want to leave? )~~
- [x] ~~Optimization cache the wrappoing results locally for faster schedule.~~
- [x] ~~Support more routes. ???~~
- [x] ~~Create an api endpoint that returns the whole timetable in a computer readable format.~~
- [x] ~~Add .gitignore~~

## Icons and colours from

[Octicons](https://primer.style/octicons/)

## Secrets

Rename secrets.example.php to secrets.php
```
cp secrets.example.php secrets.php
```
