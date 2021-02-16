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

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "33%" },
  { name: "name", label: "Nome", width: "33%" },
  {
    name: "categories",
    label: "Categorias",
    width: "33%",
    options: {
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
    width: "4%",
    options: {
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
      customBodyRender: (value) => (
        <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>
      ),
    },
  },
  {
    name: "actions",
    label: "Ações",
    width: "13%",
    options: {
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
  });

  useEffect(() => {
    filterManager.pushHistory();
    (async () => {
      setLoading(true);
      try {
        const { search, ...params } = debouncedFilter as any;
        const { data } = await GenreResource.list({
          ...params,
          search: search?.value !== undefined ? search.value : search,
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
  }, [debouncedFilter, snackbar, isMountedRef]);

  useEffect(() => {
    filterManager.replaceHistory();
    // eslint-disable-next-line
  }, []);

  const options: MUIDataTableOptions = {
    serverSide: true,
    searchText: filter.search as any,
    page: filter.page - 1,
    rowsPerPage: filter.perPage,
    rowsPerPageOptions,
    count: totalRecords,
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
