import { MUIDataTableColumn } from "mui-datatables";
import {useState,useReducer, Dispatch, Reducer} from "react";
import {reducer, Creators} from "../store/filter";
import { State as FilterState, Actions as FilterActions } from "../store/filter/types";
import { useDebounce } from 'use-debounce';
import { useHistory } from "react-router";
import { History } from "history";
import { isEqual } from "lodash";
import { yup } from "../utils/yup";

export const useFilter = (options: FilterManagerOptions) => {
  const history = useHistory();
  const filterManager = new FilterManager({...options, history});
  const INITIAL_STATE = filterManager.getStateFromURLSearchParams();
  const [filter, dispatch] = useReducer<Reducer<FilterState, FilterActions>>(reducer, INITIAL_STATE);
  const [totalRecords, setTotalRecords] = useState<number>(0);
  const [debouncedFilter] = useDebounce(filter, options.debounceTime ?? 0 );
  filterManager.state = filter;
  filterManager.dispatch = dispatch;

  return {
    filterManager,
    filter,
    debouncedFilter,
    dispatch,
    totalRecords,
    setTotalRecords
  }
};

export interface FilterManagerOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  debounceTime?: number;
  history?: History;
}

export class FilterManager {
  schema: any;
  state?: FilterState;
  dispatch?: Dispatch<FilterActions>;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  history?: History;

  constructor({columns, rowsPerPage, rowsPerPageOptions, history}: FilterManagerOptions) {
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.rowsPerPageOptions = rowsPerPageOptions;
    this.history = history;
    this.createValidationSchema();
  }

  changeSearch(searchText: string | null) {
    this.dispatch?.(Creators.setSearch(searchText))
  }

  changePage(currentPage: number) {
    this.dispatch?.(Creators.setPage(currentPage + 1))
  }

  changeRowsPerPage(perPage: number) {
    this.dispatch?.(Creators.setPerPage(perPage))
  }

  changeColumnSort(column: string, dir: string) {
    if(column && dir !== 'none') {
      this.dispatch?.(Creators.setOrder(column, dir))
    } else {
      this.dispatch?.(Creators.setOrder(null, null))
    }
  }

  getSearchText() {
    const search = this.state?.search as any;
    return search?.value !== undefined ? search.value : search;
  }

  replaceHistory() {
    this.history?.replace({
      pathname: this.history?.location.pathname,
      search: `?${this.getURLSearchParams()}`,
      state: this.state
    });
  }

  pushHistory() {
    const oldState = this.history?.location.state
    if(isEqual(oldState, this.state)) {
      return;
    }

    this.history?.push({
      pathname: this.history?.location.pathname,
      search: `?${this.getURLSearchParams()}`,
      state: {
        ...this.state,
        search: this.getSearchText()
      }
    });
  }

  getStateFromURLSearchParams() {
    const queryParams = new URLSearchParams(this.history?.location.search.substr(1))
    return this.schema.cast({
      search: queryParams.get('search'),
      page: queryParams.get('page'),
      perPage: queryParams.get('perPage'),
      sort: queryParams.get('sort'),
      dir: queryParams.get('dir'),
    })
  }

  private getURLSearchParams() {
    const search = this.getSearchText();
    const format = {
      ...(search && search !== '' && {search: search}),
      ...(this.state?.page !== 1 && {page: this.state?.page}),
      ...(this.state?.perPage !== 15 && {perPage: this.state?.perPage}),
      ...(this.state?.sort && this.state?.dir !== 'none' && {
        sort: this.state?.sort,
        dir: this.state?.dir
      })
    }
    return new URLSearchParams(format as any).toString();
  }

  private createValidationSchema() {
    this.schema = yup.object().shape({
      search: yup.string().transform((value) => !value ? undefined : value ).default(""),
      page: yup.number().transform((value) => isNaN(value) || parseInt(value) < 1 ? undefined : value ).default(1),
      perPage: yup.number().oneOf(this.rowsPerPageOptions).transform((value) => isNaN(value) ? undefined : value ).default(this.rowsPerPage),
      sort: yup.number().nullable().transform((value) => {
        const columnsName = this.columns.filter((column) => !column.options || column.options.sort !== false).map(column => column.name)
        return columnsName.includes(value) ? value : undefined
      }).default(null),
    })
  }
}

export default useFilter;