const express = require('express')
const socketio = require('socket.io');
const path = require('path')
const uuid = require("uuid");
const app = express()
const SocketData = require('./model/socket')
const cors = require('cors')
const socketConnection = require('./model/socket')
const userSocket = require('./model/userSocket')



app.use(express.static(path.resolve(__dirname, 'client')));


const server = app.listen(1234, () => {
    console.log('Server running!')
});




app.use(cors())
const io = require('socket.io')(server, {
    cors: {
      origin: '*',
    }
  });

io.on('connection', (socket) => {
    let deviceId
    console.log('New connection in server file', `${socket.id}`)

    const listener = (...data) => {
        console.log(data, "listen");
    }

    socket.on("receive", listener);

    // for mobile to mobile----------------------------------------------------------------

    // receive data from client
    socket.on('message', async (data) => {
        socket.broadcast.emit('message', data)
        let matchDeviceId = await SocketData.aggregate([
            {
                $match: { deviceToken: data.deviceToken }
            }
        ])
        console.log(data, "check exisiting device id", matchDeviceId)
        if (matchDeviceId.length == 0) {
            console.log("hii")
            SocketData.create({ ...data })

        }
        else {
            console.log("bye")
            await SocketData.findOneAndUpdate(
                { deviceToken: matchDeviceId[0].deviceToken },
                { $set: { deviceLatitude: data.deviceLatitude, deviceLongitude: data.deviceLongitude } },
                { new: true }
            )

        }
        console.log(data, "Message")

    })

    //-----------------------------------------------------------------------------------------------


    // Create another emitter for the 'chat' event
    socket.on('chat', async(data) => {
        console.log('Received a chat message:', data);
        // Emit the 'chat' event to all connected clients
        // let updatedLocation = await userSocket.aggregate([
        //     {
        //         $match: { deviceToken: data.deviceToken }
        //     }
        // ])
        console.log(data.timestamp, "check exisiting device id")
        // if (updatedLocation.length == 0) {
            let newDate = new Date(data.timestamp).toLocaleString()
            console.log("hii",newDate)
            let newTime = newDate.split(",")
            console.log("hii",newTime)

            userSocket.create({
                deviceToken:data.deviceToken,
                deviceLatitude:data.deviceLatitude,
                deviceLongitude:data.deviceLongitude,
                userId:data.userId,
                date:newTime[0],
                time:newTime[1]
            })

            

        // }
        // else {
        //     console.log("bye")
        //     await userSocket.findOneAndUpdate(
        //         { deviceToken: updatedLocation[0].deviceToken },
        //         { $set: { deviceLatitude: data.deviceLatitude, deviceLongitude: data.deviceLongitude } },
        //         { new: true }
        //     )

        // }
        io.emit('chat', data);
    });

    // Create another emitter for the 'chat' event
    socket.on('fetch_lat_long', async(data) => {
        console.log("hii")
        console.log('Received a chat message:', data);
        console.log(data,"Check input data")
        // Emit the 'chat' event to all connected clients
        let finalData = await userSocket.aggregate([
            {
                $match: { deviceToken: data.token }
            },
            {
                $project: {
                    _id: 0,
                    deviceToken: 0,
                    timestamps:0,
                    __v: 0,
                }
            }
        ])
        console.log(finalData[finalData.length-1],"Check updated latlong")
        // io.emit('fetch_lat_long', finalData[0]);
        io.emit('fetch_lat_long', finalData[finalData.length-1]);
    });


})
