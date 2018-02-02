// Require all dependencies.
var fs = require('fs');
var redisClient = require('redis').createClient();

// Get the report date.
var reportDate = Date.now();

// Error handling for redis
redisClient.on("error",function(err){
	console.log("Redis Error - " + err);
});

redisClient.lrange("queues:lead-sending-processing",0,-1,function(err,replies){
	replies.forEach(function(reply,index){
		redisClient.lpush('queues:additional-platform-processing',JSON.stringify(reply));
	});

	// delete current lead-sending-processing
	redisClient.ltrim('queues:lead-sending-processing',-1,0,function(err){
		console.log("Completed");
		process.exit();
	});
});