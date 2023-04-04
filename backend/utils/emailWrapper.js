var nodeMailer = require('nodemailer-wrapper');
 
var transportConfig = {
  transportType: 'smtp',
  config: {
    host: process.env.MAILER_HOST, // hostname
    secureConnection: false, // TLS requires secureConnection to be false
    port: 587, // port for secure SMTP
    auth: {
        user: process.env.MAILER_USER,
        pass: process.env.MAILER_PASS
    },
    // use up to 20 parallel connections
    maxConnections: 25,
    // do not send more than 10 messages per connection
    maxMessages: 10,
    tls: {
      rejectUnauthorized: false // for non-authorized mail server
    }
  }
};
 
// use your mongodb address
var mongodbConfig = 'mongodb://localhost:27017/mailerDemo';
 
// create new wrapper instance
var mailer = new nodeMailer(mongodbConfig, transportConfig);
 

var mail1 = {
  from: process.env.MAILER_USER,
  to: 'sweety@yopmail.com',
  subject: 'hello world',
  text: 'hello world!',
  html: '<b>Hello World! </b>'
}

 
// var mail2 = {
//   from: 'your-mail@mail-server',
//   to: 'target-mail-2@mail-server',
//   subject: 'hello world',
//   text: 'hello world!',
//   html: '<b>Hello World! </b>',
//   attachments: [{ 
//     filename: 'README.md',
//     path: './demo.txt'
//   }]
// };
 
mailer.prepareMail(mail1);
// mailer.prepareMail(mail2);
 
mailer.saveMails(function(err) {
  console.info('mails have been saved !');
 
  mailer.send(function(err) {
    if (err) console.log(err);
  });
});