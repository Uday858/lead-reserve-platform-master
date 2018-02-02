// Require all dependencies.
var fs = require('fs');
var redisClient = require('redis').createClient();

// Get the report date.
var reportDate = Date.now();

// Error handling for redis
redisClient.on("error",function(err){
	console.log("Redis Error - " + err);
});

// empty the additional-platform-processing
redisClient.ltrim('queues:additional-platform-processing',-1,0,function(err){
	console.log("Completed");
	process.exit();
});