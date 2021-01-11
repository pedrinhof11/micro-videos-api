import { httpVideo } from ".";
import { Category } from "../types/models";
import AbstractResource from "./AbstractResource";

const CategoryResource = new AbstractResource<Category>(
  httpVideo,
  "categories"
);

export default CategoryResource;
