const mongoose = require('mongoose')

mongoose.connect(process.env.MONGODB_URI,{
    useUnifiedTopology:true,
    useNewUrlParser:true
}).then(()=>{
    console.log("Database is connected..")
}).catch(()=>{
    console.log("There is error in your mongodb connection..")
})
