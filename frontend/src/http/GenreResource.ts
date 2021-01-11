import { httpVideo } from ".";
import { Genre } from "../types/models";
import AbstractResource from "./AbstractResource";

const GenreResource = new AbstractResource<Genre>(httpVideo, "genres");

export default GenreResource;
