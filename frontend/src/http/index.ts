import Axios from "axios";

export const httpVideo = Axios.create({
  baseURL: process.env.REACT_APP_MICRO_VIDEO_API_URL
})