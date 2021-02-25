import { MUIDataTableColumn } from "mui-datatables";
import { useState, useReducer, Dispatch, Reducer, useEffect } from "react";
import { reducer, Creators } from "../store/filter";
import {
  State as FilterState,
  Actions as FilterActions,
} from "../store/filter/types";
import { useDebounce } from "use-debounce";
import { useHistory } from "react-router";
import { History } from "history";
import { isEqual } from "lodash";
import { yup } from "../utils/yup";
import { MUIDataTableRefComponent } from "../components/Table/BaseTable";

export interface UseFilterOptions {
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  tableRef: React.RefObject<MUIDataTableRefComponent>;
  debounceTime?: number;
  extraFilter?: ExtraFilter
}
export interface ExtraFilter {
  getStateFromUrl: (queryParams: URLSearchParams) => any,
  formatSearchParams: (debouncedState: FilterState) => any
  createValidationSchema: () => any
}
export interface FilterManagerOptions extends UseFilterOptions {
  history: History;
}

export const useFilter = (options: UseFilterOptions) => {
  const history = useHistory();
  const filterManager = new FilterManager({ ...options, history });
  const INITIAL_STATE = filterManager.getStateFromURLSearchParams();
  const [filter, dispatch] = useReducer<Reducer<FilterState, FilterActions>>(
    reducer,
    INITIAL_STATE
  );
  const [totalRecords, setTotalRecords] = useState<number>(0);
  const [debouncedFilter] = useDebounce(filter, options.debounceTime ?? 0);
  filterManager.state = filter;
  filterManager.debouncedFilter = debouncedFilter;
  filterManager.dispatch = dispatch;

  useEffect(() => {
    filterManager.replaceHistory();
    // eslint-disable-next-line
  }, []);

  return {
    filterManager,
    filter,
    debouncedFilter,
    dispatch,
    totalRecords,
    setTotalRecords,
  };
};

export class FilterManager {
  schema: any;
  state?: FilterState;
  debouncedFilter?: FilterState;
  dispatch?: Dispatch<FilterActions>;
  columns: MUIDataTableColumn[];
  rowsPerPage: number;
  rowsPerPageOptions: number[];
  tableRef: React.RefObject<MUIDataTableRefComponent>;
  history?: History;
  extraFilter?: ExtraFilter;

  constructor({
    columns,
    rowsPerPage,
    rowsPerPageOptions,
    history,
    tableRef,
    extraFilter
  }: FilterManagerOptions) {
    this.columns = columns;
    this.rowsPerPage = rowsPerPage;
    this.rowsPerPageOptions = rowsPerPageOptions;
    this.history = history;
    this.tableRef = tableRef;
    this.extraFilter = extraFilter;
    this.createValidationSchema();
  }

  changeSearch(searchText: string | null) {
    this.dispatch?.(Creators.setSearch(searchText));
  }

  changePage(currentPage: number) {
    this.dispatch?.(Creators.setPage(currentPage + 1));
  }

  changeRowsPerPage(perPage: number) {
    this.dispatch?.(Creators.setPerPage(perPage));
  }

  changeColumnSort(column: string, dir: string) {
    if (column && dir !== "none") {
      this.dispatch?.(Creators.setOrder(column, dir));
    } else {
      this.dispatch?.(Creators.setOrder(null, null));
    }
    this.tableRef.current?.changePage(0);
  }

  changeExtraFilter(data: any) {
    this.dispatch?.(Creators.updateExtraFilter(data))
  }

  resetFilter() {
    const INITIAL_STATE = {
      ...this.schema.cast({}),
      search: {value: null, update: true}
    };
    this.dispatch?.(Creators.resetState(INITIAL_STATE));
    this.tableRef.current?.changeRowsPerPage(this.rowsPerPage);
    this.tableRef.current?.changePage(0);
  }

  getSearchText() {
    const search = this.debouncedFilter?.search as any;
    return search?.value !== undefined ? search.value : search;
  }

  replaceHistory() {
    this.history?.replace({
      pathname: this.history?.location.pathname,
      search: `?${this.getURLSearchParams()}`,
      state: this.debouncedFilter,
    });
  }

  pushHistory() {
    const oldState = this.history?.location.state;
    if (isEqual(oldState, this.debouncedFilter)) {
      return;
    }

    this.history?.push({
      pathname: this.history?.location.pathname,
      search: `?${this.getURLSearchParams()}`,
      state: {
        ...this.debouncedFilter,
        search: this.getSearchText(),
      },
    });
  }

  getStateFromURLSearchParams() {
    const queryParams = new URLSearchParams(
      this.history?.location.search.substr(1)
    );
    return this.schema.cast({
      search: queryParams.get("search"),
      page: queryParams.get("page"),
      perPage: queryParams.get("perPage"),
      sort: queryParams.get("sort"),
      dir: queryParams.get("dir"),
      ...( 
        this.extraFilter && {
          extraFilter: this.extraFilter.getStateFromUrl(queryParams)
        }
      )
    });
  }

  private getURLSearchParams() {
    const search = this.getSearchText();
    const format = {
      ...(search && search !== "" && { search: search }),
      ...(this.debouncedFilter?.page !== 1 && {
        page: this.debouncedFilter?.page,
      }),
      ...(this.debouncedFilter?.perPage !== 15 && {
        perPage: this.debouncedFilter?.perPage,
      }),
      ...(this.debouncedFilter?.sort &&
        this.debouncedFilter?.dir !== "none" && {
          sort: this.debouncedFilter?.sort,
          dir: this.debouncedFilter?.dir,
        }),
      ...(
        this.extraFilter && this.debouncedFilter && this.extraFilter.formatSearchParams(this.debouncedFilter)
      )
    };
    return new URLSearchParams(format as any).toString();
  }

  private createValidationSchema() {
    this.schema = yup.object().shape({
      search: yup
        .string()
        .transform((value) => (!value ? undefined : value))
        .default(""),
      page: yup
        .number()
        .transform((value) =>
          isNaN(value) || parseInt(value) < 1 ? undefined : value
        )
        .default(1),
      perPage: yup
        .number()
        .transform((value) =>
          this.rowsPerPageOptions.includes(value) || isNaN(value)
            ? undefined
            : value
        )
        .default(this.rowsPerPage),
      sort: yup
        .string()
        .nullable()
        .transform((value) => {
          const columnsName = this.columns
            .filter(
              (column) => !column.options || column.options.sort !== false
            )
            .map((column) => column.name);
          return columnsName.includes(value) ? value : undefined;
        })
        .default(null),
      dir: yup
        .string()
        .nullable()
        .transform((value) =>
          !value || !["asc", "desc", "none"].includes(value.toLowerCase())
            ? undefined
            : value
        )
        .default(null),
      ...(this.extraFilter && {extraFilter: this.extraFilter.createValidationSchema()})
    });
  }
}

export default useFilter;
