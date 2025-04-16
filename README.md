# Livesport - Central Data Storage
### Central Data Storage task for summer internship at Livesport

## Database model
![Database model](database_model.svg)

## Setup - how to launch app using Docker
* **Requirements** - installed Docker, docker-compose
* Go to root of the project - `cd Livesport-Central-Data-Storage/`
* Start the whole application with database - `docker-compose up`, you can put argument `-d` to run it on background
* To create tables for database scheme, we must execute migration, which will also insert few data - `docker exec -it symfony_app php bin/console doctrine:migrations:migrate`
* The API will be available at `http://localhost:8000` and database on port `5432`, **be ensured that you have both of these ports free on your device**
* to end whole containerized app, just enter `Ctrl+C` on terminal where you launched it, if you launch application with `-d` argument then type to terminal `docker-compose down`

## Script for simulating matches
* I manage to integrate the script to be working with Symfony app, so I made it, that you can execute it as command to Symfony app. The source code of the script/command can be found on `/src/Command/SimulateMatchCommand`
* You can execute it within container - `docker exec -it symfony_app php bin/console app:simulate-match <NumberOfEvents>`, where `<NumberOfEvents>` is optional argument of number of another events not including the mandantory ones, default of `<NumberOfEvents>` is 2
* **Keep in mind that for executing script the app must be turned on!**


## Available API endpoints
#### You can also find endpoints on [http://localhost:8000/api/doc](http://localhost:8000/api/doc), if you have running app
### Teams
* `GET /teams` - get all teams
* `GET /teams/{teamId}` - get specific team using ID
* `POST /teams` - create a new team
* `PUT /teams/{teamId}` - update a team specified by its ID
* `DELETE /teams/{teamId}` - delete a team specified by its ID
### Matches
* `GET /matches` - get all matches
* `GET /matches/{matchId}` - get specific match using ID
* `POST /matches` - create a new match
* `PUT /matches/{matchId}` - update a match specified by its ID
* `DELETE /matches/{matchId}` - delete a match specified by its ID
### Events
* `GET /matches/{matchId}/events` - get all events of specific match
* `GET /matches/{matchId}/events/${eventId}` - get specific event of match
* `POST /matches/{matchId}/events` - create a new event of match
* `PUT /matches/{matchId}/events/${eventId}` - update event of match
* `DELETE /matches/{matchId}/events/${eventId}` - delete event of match


## Contact
* If you have some problem with code, execution of the app or other stuff related with this project, you can contact me on email [ratimec.david99@gmail.com](mailto:ratimec.david99@gmail.com) and I can help you :)