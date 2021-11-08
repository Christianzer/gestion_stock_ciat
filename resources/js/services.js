const https = require('https')
import axios from "axios"
export default axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Accept': 'application/json'
  },
  httpsAgent: new https.Agent({
    rejectUnauthorized: false
  })
})
