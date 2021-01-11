import { httpVideo } from ".";
import { CastMember } from "../types/models";
import AbstractResource from "./AbstractResource";

const CastMemberResource = new AbstractResource<CastMember>(
  httpVideo,
  "cast-members"
);

export default CastMemberResource;
