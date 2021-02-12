import { Chip, IconButton, MuiThemeProvider } from "@material-ui/core";
import EditIcon from "@material-ui/icons/Edit";
import { MUIDataTableOptions } from "mui-datatables";
import { useSnackbar } from "notistack";
import React, { useCallback, useEffect, useReducer, useState } from "react";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  TableColumn,
} from "../../components/Table/BaseTable";
import ResetFilterButton from "../../components/Table/ResetFilterButton";
import CategoryResource from "../../http/CategoryResource";
import reducer, { INITIAL_STATE, Creators } from "../../store/search";
import { Category } from "../../types/models";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";

const columns: TableColumn[] = [
  { name: "id", label: "ID", options: { sort: false }, width: "33%" },
  {
    name: "name",
    label: "Nome",
    width: "40%",
    options: { sort: true, sortThirdClickReset: true },
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

const CategoriesTable = () => {
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [total, setTotal] = useState<number>(0);
  const [requestParams, dispatch] = useReducer(reducer, INITIAL_STATE);

  const options: MUIDataTableOptions = {
    serverSide: true,
    searchText: requestParams.search as any,
    page: requestParams.page - 1,
    rowsPerPage: requestParams.perPage,
    count: total,
    customToolbar: () => (
      <ResetFilterButton
        onClick={() => { dispatch(Creators.resetState())}}
      ></ResetFilterButton>
    ),
    onSearchChange: (searchText) => dispatch(Creators.setSearch(searchText ?? '')),
    onChangePage: (currentPage) => dispatch(Creators.setPage(currentPage + 1)),
    onChangeRowsPerPage: (perPage) => dispatch(Creators.setPerPage(perPage)),
    onColumnSortChange:(column, dir) =>  dispatch(Creators.setOrder(column, dir)),
  };

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const { search, ...params } = requestParams as any
      const { data } = await CategoryResource.list({ 
        ...params, 
        search: search?.value !== undefined ? search.value : search 
      });
      if (isMountedRef.current) {
        setCategories(data.data);
        setTotal(data.meta!.total);
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
  }, [requestParams, isMountedRef, snackbar]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return (
    <MuiThemeProvider theme={makeActionThemes(columns.length - 1)}>
      <BaseTable
        title=""
        columns={columns}
        data={categories}
        loading={loading}
        options={options}
        debounceSearch={500}
      />
    </MuiThemeProvider>
  );
};

export default CategoriesTable;
