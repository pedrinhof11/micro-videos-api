interface BaseModel {
  readonly id: string;
  readonly created_at?: string;
  readonly updated_at?: string;
  readonly deleted_at?: string | null;
}

export interface Category extends BaseModel {
  name: string;
  description: string;
  is_active: boolean;
}

export enum CastMemberTypesEnum {
  Diretor = 1,
  Ator = 2,
}

export interface CastMember extends BaseModel {
  name: string;
  type: keyof typeof CastMemberTypesEnum;
}

export interface Genre extends BaseModel {
  name: string;
  is_active: boolean;
  categories: Category[];
}
