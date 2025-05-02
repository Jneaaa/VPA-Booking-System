const express = require('express');
const router = express.Router();
const db = require('../db/connection');

router.post('/book', (req, res) => {
  const { name, facility, date, time_start, time_end, purpose } = req.body;
  const sql = 'INSERT INTO reservations (name, facility, date, time_start, time_end, purpose) VALUES (?, ?, ?, ?, ?, ?)';
  db.query(sql, [name, facility, date, time_start, time_end, purpose], (err, result) => {
    if (err) throw err;
    res.send('Reservation successfully recorded!');
  });
});

module.exports = router;

