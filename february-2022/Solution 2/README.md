## PHP CHALLENGE
### Description: 

My solution is a simple API developed using Laravel framework, the API receives requests from other internal services, and resend the request to the mail delivery service in a way that validates the requests before sending them, and providing a way to track the state of each requests, also ensuring the receiving of the requests to the mail delivery service, and finally offering a priority option for email that has to be dealt with NOW.

### Assumptions:
**Mail Delivery Service offers a priority option with a default value set to NORMAL, and can be set to NOW**

 - NORMAL: this means any request will be added to the end of the
   current stack of requests and for the response of the request return
   an acceptance state indicating the receiving of the request.
   
 - NOW: this means the delivery service will pause dealing with NORMAL
   requests and deal with this request immediately, and return the
   results of processing this request as a response to the request
   without any delay.


### Problems & Solutions:
**Mail delivery service is down? now what!**
in this case, my solution was to run a schedules task every minute in the background, this task recalls all requests from database with a state of **NOT ACCEPTED** and resends the request to the mail delivery service, and update the state to **ACCEPTED** if the service received the request this time.

### API ENDPOINTS:
**/send :**
this endpoint receives a post call with a json body containing the following fields:

    {
    	"sender": "sender@example.com",
    	"recipient": "recipient@example.com",
    	"message": "Hello, World!"
    }
if any of those fields are missing the endpoint returns: (http code 500)

    {
	    "STATUS":  "DENIED",
	    "ERROR":  "MISSING FIELDS"
    }
in case sender or recipient are not valid emails: (http code 500)

    {
    	"STATUS":  "DENIED",
    	"ERROR":  "EMAILS ARE NOT VALID"
    }
in case every thing is valid: (http code 200)

    {
	    "STATUS":  "ACCEPTED",
	    "REQUEST_ID":  "1a4ef1898d325d2fcd3ea43cc513ec35a83060d803668a81a1e75c02da99c1c0"
    }
or in case the mail delivery service is down:

    {
	    "STATUS":  "NOT ACCEPTED",
	    "REQUEST_ID":  "be7aefa01fea6935c3d7800574b6f299677f4a65fe1069ced33b88ca00ea7787",
	    "MSG":  "MAIL DELIVERY SERVICE IS DOWN,WE WILL RESEND THE REQUEST AUTOMATICALLY WHEN IT GOES UP AGAIN"
    }

this is all there is to the **/send** endpoint.

**/status/{request_id} :**
this endpoint accepts GET requests with replacing the {request_id} to the request id provided by the /send endpoint
##### example:

    http://example.com/status/be7aefa0...f4a..0ea7787

response:

    {
	    "status":  "NOT ACCEPTED" | "ACCEPTED" | "DELIVERED" | "FAILED" | "REJECTED",
	    "request_id":  "be7aefa0...f4a..0ea7787"
    }
that all there is to **/status/{request_id}** endpoint.

**/callback :**
this endpoint receives POST request, its objective is to receive the webhook calls from the mail delivery service when processing the NORMAL priority requests.

##### request example:

    {
	    "ID":  "be7aefa0...f4a..0ea7787",
	    "STATUS":  "REJECTED"
    }

##### response:

    {}
with http code 200 incase every thing went well
or

    {
	    "ERROR":  "INVALIDE REQUEST"
    }
in case of a bad request.

## Mail Delivery Service ( Mockup )

for the sake of this challenge I created a mockup mail delivery service, which will play the role of a real life mail delivery service.

### /api/emails:
this end point accepts a post request

    {
	    "request_id": "be7aefa0...f4a..0ea7787",
    	"sender": "sender@example.com",
    	"recipient": "recipient@example.com",
    	"priority": "NORMAL" | "NOW",
    	"message": "Hello, World!"
    }

another component to this mail delivery service mockup is a scheduled task which plays the role of the webhook, which runs every minute and updates any request with a state of "ACCEPTED" into "DELIVERED" | "REJECTED" | "FAILED" by sending a request to **/callback** endpoint in out project.

with this all the components are explained.

## Project setup

 - to setup this project, start by cloning this repository into your
   computer.
 - edit the .env.example file and rename it to .env
 - make vhost to access your project easily.
 - inside the project folder run:
`php artisan migrate`
 - if in windows please add a task from task scheduler app on windows.
`path_to_php.exe path_to_project\artisan schedule:run`
