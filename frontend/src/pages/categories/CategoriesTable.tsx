import { Chip, IconButton, MuiThemeProvider } from "@material-ui/core";
import EditIcon from "@material-ui/icons/Edit";
import { MUIDataTableOptions } from "mui-datatables";
import { useSnackbar } from "notistack";
import React, { useCallback, useEffect, useState } from "react";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  TableColumn,
} from "../../components/Table/BaseTable";
import ResetFilterButton from "../../components/Table/ResetFilterButton";
import CategoryResource from "../../http/CategoryResource";
import { Category } from "../../types/models";
import { dateFormatFromIso, useIsMountedRef } from "../../utils";
interface Pagination {
  page: number;
  perPage: number;
}

interface Order {
  sort?: string | null;
  dir?: string | null;
}
interface Search {
  search: string;
}

interface RequestParams extends Search, Pagination, Order {}

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
  const initialState = {
    search: "",
    page: 1,
    perPage: 10,
    sort: null,
    dir: null,
  };
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const [total, setTotal] = useState<number>(0);
  const [requestParams, setRequestParams] = useState<RequestParams>(
    initialState
  );

  const handleSearchChange = (value: string | null): void => {
    setRequestParams((prevState) => ({
      ...prevState,
      search: value ?? "",
      page: 1,
    }));
  };

  const handleChangePage = (currentPage: number) => {
    setRequestParams((prevState) => ({
      ...prevState,
      page: currentPage + 1,
    }));
  };

  const handleChangeRowsPerPage = (perPage: number) => {
    setRequestParams((prevState) => ({
      ...prevState,
      perPage: perPage,
    }));
  };

  const handleColumnSortChange = (column: string, dir: "asc" | "desc") => {
    setRequestParams((prevState) => ({
      ...prevState,
      sort: column,
      dir,
    }));
  };

  const options: MUIDataTableOptions = {
    serverSide: true,
    searchText: requestParams.search as string,
    page: requestParams.page - 1,
    rowsPerPage: requestParams.perPage,
    count: total,
    customToolbar: () => (
      <ResetFilterButton
        onClick={() => {
          setRequestParams({
            ...initialState,
            search: {
              value: initialState.search
            } as any
          });
        }}
      ></ResetFilterButton>
    ),
    onSearchChange: handleSearchChange,
    onChangePage: handleChangePage,
    onChangeRowsPerPage: handleChangeRowsPerPage,
    onColumnSortChange: handleColumnSortChange,
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
