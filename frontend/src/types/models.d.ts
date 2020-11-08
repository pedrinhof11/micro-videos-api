interface BaseModel {
  id: string,
  created_at?: string,
  updated_at?: string,
  deleted_at?: string
}

export interface Category extends BaseModel {
  name: string,
  is_active: boolean
}

export enum CastMemberTypesEnum {
  Diretor = 1,
  Ator = 2,
}

export interface CastMember extends BaseModel {
  name: string,
  type: number
}

export interface Genre extends BaseModel {
  name: string,
  is_active: boolean,
  categories: Category[]
}