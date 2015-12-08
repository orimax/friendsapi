#Friends API
Rest API for work with friends.
The code, certainly, can be improved, and shows only the idea of building REST API, using PHP5 and Symfony2.

##Installation

1. Clone this project.
2. Run `composer install`
3. Ensure, that mongodb is installed on your system.
4. Ensure, that mongodb driver for PHP is installed on your system.
5. Ensure, that rabbitmq-server is installed on your system.

##Used technologies
I tried to build this API with PHP7 and Symfony3. But, unfortunately, there is no PHP driver for Mongo DB for PHP7 yet. So.

1. Framework Symfony2 and PHP5.6.
2. MongoDBBundle to work with MongoDB and Doctrine ODM.
3. RESTified with FoSRestBundle.
4. RabbitMQBundle allows use Queue-Centric Workflow pattern for planning of durable operations, that cannot be calculated immediately.
5. PHPUnit for functional tests.

Tested with Nginx + PHP-FPM.
Load tests run, using JMeter.

##API docs

###Authentication
The authentication is performed using API key of the user, that uniquely identifies a user.

To authenticate, pass user's *apikey* in request headers.

You have to specify *apikey* in each request.

###Get the list of friends
[GET] /friends/list

Returns the friends of the user by apikey.

**Response fields**

*status* - success | failure

*data* - array of response data. Contains array of errors, if failure. If success, contains the  the array of friends with the following fields:

- *name* - the user's name
- *id* - the user's unique identifier.

*[errorCode]* - optionally for failure. See the Error codes section.

###Apply for friendship
[PUT] /friends/addfriend

User A applies for friendship with user B. If user B applied for the friendship earlier, users A and B will appear in friends lists of each other, otherwise, user B will get a friendship request.

**Response fields**

*status* - success | failure

*data* - will be empty if success, and will contain the array of errors if failure.

*[errorCode]* - optionally for failure. See the Error codes section.

###Accept friendship request
[PUT] /friends/request/accept

**Request parameters**

*friendId* - the ID of the user, the friendship request of which must be accepted.

Places the user to the friends list, if the user is in the list of friendshipRequests.

**Response fields**

*status* - success | failure

*data* - will be empty if success, and will contain the array of errors if failure.

*[errorCode]* - optionally for failure. See the Error codes section.

###Decline friendship request

[DELETE] /friends/request/decline

**Request parameters**

*userId* - the ID of the user, the friendship request of which must be accepted.

Deletes specified user from friendshipRequests.

**Response fields**

*status* - success | failure

*data* - will be empty if success, and will contain the array of errors if failure.

*[errorCode]* - optionally for failure. See the Error codes section.

###Get friends of friends

[GET] /friends/friendsoffriends?depth={depth}

*depth* - specify here, what depth of you want to explore. It means how deep friends of friends (... of friends) list will be.

Returns the list of friends of friends of specified depth. It is an asynchronous method, that will be pushed to the queue. With each request you will get the progress of your initial request.

**Response fields**

*status* - success | failure

*data* - array of response data. Contains array of errors, if failure. If success, contains the next fields:

- *progress* - the current progress of your request. The list of friends will be specified when the progress equals 100. Can be set to integers from 0 to 100.
- *friends* - the array of friends with the following fields:
    - *name* - the user's name
    - *id* - the user's unique identifier.
    
*[errorCode]* - optionally for failure. See the Error codes section.

###Error codes
If the status is failure, you will additional parameter in your response: errorCode.

Use this manual to decode the errors.

*1000*: The API key is not specified.

*1001*: You want to perform any action with friendship request, that does not exist.

*1002*: Specified API key does not belong to any existing user.

*0*: The type of error has not grouped yet. Use the data explanation.