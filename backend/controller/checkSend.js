const nodemailer = require('nodemailer');

// create reusable transporter object using SMTP transport
let transporter = nodemailer.createTransport({
    host: process.env.MAILER_HOST,
                port: 465,
                secure: true, // use SSL
                auth: {
                    user: process.env.MAILER_USER,
                    pass: process.env.MAILER_PASS 
                }
});

// create array of email addresses to send to
let emailList = ['a@gmail.com','b@gmail.com','c@gmail.com','d@gmail.com','e@gmail.com','f@gmail.com',
'g@gmail.com','h@gmail.com','i@gmail.com','j@gmail.com','k@gmail.com','l@gmail.com','m@gmail.com',
'n@gmail.com','o@gmail.com','p@gmail.com','q@gmail.com','r@gmail.com','s@gmail.com','t@gmail.com',
'u@gmail.com','v@gmail.com','w@gmail.com','x@gmail.com','y@gmail.com',"z@yopmail.com","sweety@yopmail.com"]
const send = (to,body) => {

// divide emailList into batches of 25
let batches = [];
while (emailList.length > 0) {
    batches.push(emailList.splice(0, 25));
}

// send emails in batches
batches.forEach(async (batch) => {
    try {
        // create reusable message object
        let message = {
            from: process.env.MAILER_USER,
            to: batch.join(', '), // join batch of 25 emails into a comma-separated string
            subject: 'Subject',
            text: 'Hello world!',
            html: body,
            // html: '<p>Hello, this is a test email!</p>',
            oTracking:true

        };
    
        // send message using transporter
        let info = await transporter.sendMail(message);
        console.log('Message sent: %s', info.messageId);
    } catch (error) {
        console.error(error);
    }
})
}

module.exports = send
