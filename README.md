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
### Teams
* `GET /teams`
* `GET /teams/{teamId}`
* `POST /teams`
* `PUT /teams/{teamId}`
* `DELETE /teams/{teamId}`
### Matches
* `GET /matches`
* `GET /matches/{matchId}`
* `POST /matches`
* `PUT /matches/{matchId}`
* `DELETE /matches/{matchId}`
### Events
* `GET /matches/{matchId}/events`
* `GET /matches/{matchId}/events/${eventId}`
* `POST /matches/{matchId}/events`
* `PUT /matches/{matchId}/events/${eventId}`
* `DELETE /matches/{matchId}/events/${eventId}`

## Contact
* If you have some problem with code, execution of the app or other stuff related with this project, you can contact me on email [ratimec.david99@gmail.com](mailto:ratimec.david99@gmail.com) and I can help you :)