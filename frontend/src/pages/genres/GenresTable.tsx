import React, { useEffect, useRef, useState } from "react";
import { Chip, IconButton, MuiThemeProvider } from "@material-ui/core";
import EditIcon from "@material-ui/icons/Edit";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  MUIDataTableRefComponent,
  TableColumn,
} from "../../components/Table/BaseTable";
import GenreResource from "../../http/GenreResource";
import { Category, Genre } from "../../types/models";
import { dateFormatFromIso } from "../../utils";
import useIsMountedRef from "../../hooks/useIsMountedRef";
import { useSnackbar } from "notistack";
import useFilter from "../../hooks/useFilter";
import ResetFilterButton from "../../components/Table/ResetFilterButton";
import { MUIDataTableOptions } from "mui-datatables";
import { yup } from "../../utils/yup";
import CategoryResource from "../../http/CategoryResource";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false, filter: false }, width: "27%" },
  { name: "name", label: "Nome", width: "20%", options: { filter: false } },
  {
    name: "categories",
    label: "Categorias",
    width: "28%",
    options: {
      sort: false,
      filterType: "multiselect",
      filterOptions: {
        names: []
      },
      customBodyRender: (categories) => {
        return (categories as any)
          .map((value: Category) => value.name)
          .join(", ");
      },
    },
  },
  {
    name: "is_active",
    label: "Ativo?",
    width: "5%",
    options: {
      filter: false,
      customBodyRender: (value) => {
        return (value as any) ? (
          <Chip label="Sim" color="primary" />
        ) : (
          <Chip label="Não" color="secondary" />
        );
      },
    },
  },
  {
    name: "created_at",
    label: "Criado em",
    width: "10%",
    options: {
      filter: false,
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
  {
    name: "actions",
    label: "Ações",
    width: "10%",
    options: {
      filter: false,
      customBodyRender: (value, tableMeta) => {
        return (
          <IconButton
            title="editar Gênero"
            color="secondary"
            component={Link}
            to={`/genres/${tableMeta.rowData[0]}/edit`}
          >
            <EditIcon fontSize="inherit" />
          </IconButton>
        );
      },
    },
  },
];

const debounceTime = 300;
const debounceSearch = 300;
const rowsPerPage = 15;
const rowsPerPageOptions = [15, 25, 50];
const GenresTable = () => {
  const [genres, setGenres] = useState<Genre[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [loading, setLoading] = useState<boolean>(false);
  const tableRef = useRef<MUIDataTableRefComponent>(null);
  const {
    filterManager,
    filter,
    debouncedFilter,
    totalRecords,
    setTotalRecords,
  } = useFilter({
    columns,
    debounceTime,
    rowsPerPage,
    rowsPerPageOptions,
    tableRef,
    extraFilter: {
      createValidationSchema() {
        return yup.object().shape({
          categories: yup.array()
          .nullable()
          .transform((value) => !value || value === "" ? undefined : value.split(','))
          .default(null)
        })
      },
      formatSearchParams(debouncedState){
        return debouncedState.extraFilter ? {
          ...(debouncedState.extraFilter.categories && { categories: debouncedState.extraFilter.categories.join(',') })
        } : undefined
      },
      getStateFromUrl(queryParams) {
        return {
          categories: queryParams.get('categories')
        }
      }
    }
  });

  const columnCategories = columns.find(c => c.name === 'categories');
  const categoriesfilterValue = filter.extraFilter?.categories;
  columnCategories!.options!.filterList = categoriesfilterValue ? [...categoriesfilterValue] : [];

  useEffect(() => {
    (async () => {
      try {
        const { data } = await CategoryResource.list({ all: true });
        if (isMountedRef.current) {
          setCategories(data.data);
          
        }
      } catch (error) {
        console.log(error);
      }
    })();
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    columnCategories!.options!.filterOptions!.names = categories.map(category => category.name)
  }, [columnCategories, categories]);

  useEffect(() => {
    filterManager.pushHistory();
    (async () => {
      setLoading(true);
      try {
        const { search, extraFilter, ...params } = debouncedFilter as any;
        const { data } = await GenreResource.list({
          ...params,
          search: search?.value !== undefined ? search.value : search,
          ...(extraFilter?.categories && {categories: extraFilter.categories})
        });
        if (isMountedRef.current) {
          setGenres(data.data);
          setTotalRecords(data.meta!.total);
        }
      } catch (error) {
        if (GenreResource.isCancel(error)) {
          return;
        }
        snackbar.enqueueSnackbar("Não possivel carregar as informações", {
          variant: "error",
        });
      } finally {
        setLoading(false);
      }
    })();
    // eslint-disable-next-line
  }, [debouncedFilter, isMountedRef, snackbar, setTotalRecords]);

  const options: MUIDataTableOptions = {
    serverSide: true,
    searchText: filter.search as any,
    page: filter.page - 1,
    rowsPerPage: filter.perPage,
    rowsPerPageOptions,
    count: totalRecords,
    onFilterChange: (column, filterList, type, changedColumnIndex ) => {
      if(type === 'reset') {
        filterManager.changeExtraFilter({categories: null}); 
      } else {
        filterManager.changeExtraFilter({
          [column as string] : filterList[changedColumnIndex].length ? filterList[changedColumnIndex] : null
        }); 
      }
     
    },
    customToolbar: () => (
      <ResetFilterButton
        onClick={() => filterManager.resetFilter()}
      ></ResetFilterButton>
    ),
    onSearchChange: (searchText) => filterManager.changeSearch(searchText),
    onChangePage: (currentPage) => filterManager.changePage(currentPage),
    onChangeRowsPerPage: (numberOfRows) =>
      filterManager.changeRowsPerPage(numberOfRows),
    onColumnSortChange: (column, dir) =>
      filterManager.changeColumnSort(column, dir),
  };

  return (
    <MuiThemeProvider theme={makeActionThemes(columns.length - 1)}>
      <BaseTable
        title=""
        ref={tableRef}
        columns={columns}
        data={genres}
        loading={loading}
        options={options}
        debounceSearch={debounceSearch}
      />
    </MuiThemeProvider>
  );
};

export default GenresTable;
