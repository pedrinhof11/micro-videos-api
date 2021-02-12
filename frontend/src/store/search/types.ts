import { AnyAction } from "redux"

export interface Pagination {
  page: number;
  perPage: number;
}

export interface Order {
  sort?: string | null;
  dir?: string | null;
}
export interface Search {
  search: { value: any, [key: string]: any } | string  | null
}

export interface State extends Search, Pagination, Order {}

export interface SetSearchAction extends AnyAction {
  search: { value: any, [key: string]: any } | string  | null
}

export interface SetPageAction extends AnyAction {
  page: number
}

export interface SetPerPageAction extends AnyAction {
  perPage: number
}

export interface SetOrderAction extends AnyAction {
  sort: string | null;
  dir: string | null;
}

export interface ResetStateAction extends AnyAction {}

export interface ActionTypes {
  SET_SEARCH: string,
  SET_PAGE: string,
  SET_PER_PAGE: string,
  SET_ORDER: string,
  RESET_STATE: string,
}
export interface ActionCreators {
  setSearch(search: SetSearchAction['search']) : SetSearchAction,
  setPage(page: SetPageAction['page']): SetPageAction,
  setPerPage(perPage: SetPerPageAction["perPage"]): SetPageAction,
  setOrder(sort: SetOrderAction['sort'], dir: SetOrderAction['dir']): SetOrderAction,
  resetState(): AnyAction
}