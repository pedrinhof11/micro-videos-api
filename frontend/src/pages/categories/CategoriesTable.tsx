import React, { useCallback, useEffect, useRef, useState } from "react";
import { Chip, IconButton, MuiThemeProvider } from "@material-ui/core";
import EditIcon from "@material-ui/icons/Edit";
import { MUIDataTableOptions } from "mui-datatables";
import { useSnackbar } from "notistack";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  TableColumn,
  MUIDataTableRefComponent,
} from "../../components/Table/BaseTable";
import ResetFilterButton from "../../components/Table/ResetFilterButton";
import CategoryResource from "../../http/CategoryResource";
import { Category } from "../../types/models";
import { dateFormatFromIso } from "../../utils";
import useFilter from "../../hooks/useFilter";
import useIsMountedRef from "../../hooks/useIsMountedRef";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "33%" },
  {
    name: "name",
    label: "Nome",
    width: "40%",
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
      customBodyRender: (value) => {
        return <span>{dateFormatFromIso(value, "dd/MM/yyyy")}</span>;
      },
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
            title="editar Categoria"
            color="secondary"
            component={Link}
            to={`/categories/${tableMeta.rowData[0]}/edit`}
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
const CategoriesTable = () => {
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [categories, setCategories] = useState<Category[]>([]);
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

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const { search, ...params } = debouncedFilter as any;
      const { data } = await CategoryResource.list({
        ...params,
        search: search?.value !== undefined ? search.value : search,
      });
      if (isMountedRef.current) {
        setCategories(data.data);
        setTotalRecords(data.meta!.total);
      }
    } catch (error) {
      if (CategoryResource.isCancel(error)) {
        return;
      }
      snackbar.enqueueSnackbar("Não possivel carregar as informações", {
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  }, [debouncedFilter, isMountedRef, snackbar, setTotalRecords]);

  useEffect(() => {
    filterManager.pushHistory();
    fetchData();
    // eslint-disable-next-line
  }, [fetchData]);

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
        data={categories}
        loading={loading}
        options={options}
        debounceSearch={debounceSearch}
      />
    </MuiThemeProvider>
  );
};

export default CategoriesTable;
