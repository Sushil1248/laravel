const nodemailer = require('nodemailer');
let cron = require('node-cron');

// transporter object with password authenticated smtp mailer
const transporterSMTP = nodemailer.createTransport({
    host: process.env.MAILER_HOST,
    port: 465,
    secure: true, // use SSL
    auth: {
        user: process.env.MAILER_USER,
        pass: process.env.MAILER_PASS 
    }
})


const send = (to, body) => {
    const mailOptions = {
        from: process.env.MAILER_USER,
        to : to,
        html: body,

    };
    return new Promise((ok, fail) => {
            transporterSMTP.sendMail(mailOptions, (error, info) => {
                if (error) {
                    console.error(error.stack);
                    return fail(error);
                }
                console.log(JSON.stringify(info));
                return ok(info);
            });
        
    });
}

module.exports = send;


