
const mongoose = require('mongoose')

const userLogsSchema = mongoose.Schema({
    user_id: {type:String},
  latitude:{type:String} ,
  longitude: {type:String},
  last_login: {type:String},
  logged_in_at:{type:String}
},{timestamps:true});

module.exports = mongoose.model("userLogs",userLogsSchema)


//save user data received from sushil