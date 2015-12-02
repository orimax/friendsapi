#Friends API
Rest API for work with friends.

##Installation

1. Clone this project.
2. Run `composer install`
3. Ensure, that mongodb is installed on your system.
4. Ensure, that mongodb driver for PHP is installed on your system.

##API docs

###Authentication
The authentication is performed using API key of the user, that uniquely identifies a user.
To authenticate, pass user's *apikey* in request headers.
You have to specify *apikey* in each request.

###Get the list of friends
[GET] /friends/list
Returns the friends of the user by apikey.

###Apply for friendship
[PUT] /friends/addfriend
User A applies for friendship with user B.
If user B applied for the friendship earlier, users A and B will appear in friends lists of each other, otherwise, user B will get a friendship request.
