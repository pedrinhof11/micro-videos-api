import { createActions, createReducer } from "reduxsauce";
import * as typings from "./types";

export const { Types, Creators } = createActions<
  typings.ActionTypes,
  typings.ActionCreators
>({
  setSearch: ["search"],
  setPage: ["page"],
  setPerPage: ["perPage"],
  setOrder: ["sort", "dir"],
  resetState: [],
});

export const INITIAL_STATE: typings.State = {
  search: null,
  page: 1,
  perPage: 10,
  sort: null,
  dir: null,
};

const setSearch = (
  state = INITIAL_STATE,
  { search }: typings.SetSearchAction
) => {
  return {
    ...state,
    search,
    page: 1,
  };
};
const setPage = (state = INITIAL_STATE, { page }: typings.SetPageAction) => {
  return {
    ...state,
    page,
  };
};
const setPerPage = (
  state = INITIAL_STATE,
  { perPage }: typings.SetPerPageAction
) => {
  return {
    ...state,
    perPage,
  };
};
const setOrder = (
  state = INITIAL_STATE,
  { sort, dir }: typings.SetOrderAction
) => {
  return {
    ...state,
    sort,
    dir,
  };
};
const resetState = () => {
  return {
    ...INITIAL_STATE,
    search: { value: null, update: true },
  };
};

export const reducer = createReducer(INITIAL_STATE, {
  [Types.SET_SEARCH]: setSearch,
  [Types.SET_PAGE]: setPage,
  [Types.SET_PER_PAGE]: setPerPage,
  [Types.SET_ORDER]: setOrder,
  [Types.RESET_STATE]: resetState,
});

export default reducer;
