import React, { useEffect, useRef, useState } from "react";
import { IconButton, MuiThemeProvider } from "@material-ui/core";
import { Edit as EditIcon } from "@material-ui/icons";
import { Link } from "react-router-dom";
import BaseTable, {
  makeActionThemes,
  MUIDataTableRefComponent,
  TableColumn,
} from "../../components/Table/BaseTable";
import { CastMember, CastMemberTypesEnum } from "../../types/models.d";
import { dateFormatFromIso } from "../../utils";
import CastMemberResource from "../../http/CastMemberResource";
import useIsMountedRef from "../../hooks/useIsMountedRef";
import { useSnackbar } from "notistack";
import useFilter from "../../hooks/useFilter";
import { MUIDataTableOptions } from "mui-datatables";
import ResetFilterButton from "../../components/Table/ResetFilterButton";
import { yup } from "../../utils/yup";

const debounceTime = 300;
const debounceSearch = 300;
const rowsPerPage = 15;
const rowsPerPageOptions = [15, 25, 50];
const castMembersNames = Object.keys(CastMemberTypesEnum).filter(x => !(parseInt(x) >= 0));

const columns: TableColumn[] = [
  { 
    name: "id", label: "ID", options: { sort: false , filter: false}, width: "25%" 
  },
  { 
    name: "name", label: "Nome", width: "40%",  options: { filter: false }
  },
  {
    name: "type",
    label: "Tipo",
    width: "10%",
    options: {
      
      filterOptions: {
        names: castMembersNames
      },
      customBodyRender: (value: any) => CastMemberTypesEnum[value as any],
    },
  },
  {
    name: "created_at",
    label: "Criado em",
    width: "10%",
    options: {
      filter: false,
      customBodyRender: (value: string) => (
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
      customBodyRender: (value: any, tableMeta: { rowData: any[] }) => {
        return (
          <IconButton
            title="editar Membro dde Elenco"
            color="secondary"
            component={Link}
            to={`/cast-members/${tableMeta.rowData[0]}/edit`}
          >
            <EditIcon fontSize="inherit" />
          </IconButton>
        );
      },
    },
  },
];


const CastMembersTable = () => {
  const isMountedRef = useIsMountedRef();
  const snackbar = useSnackbar();
  const [castMembers, setCastMembers] = useState<CastMember[]>([]);
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
          type: yup.string()
          .nullable()
          .transform((value) => !value && castMembersNames.includes(value)? undefined : value)
          .default(null)
        })
      },
      formatSearchParams(debouncedState){
        return debouncedState.extraFilter?.type ? { type: debouncedState.extraFilter.type } : undefined
      },
      getStateFromUrl(queryParams) {
        return {
          type: queryParams.get('type')
        }
      }
    }
  });
  const columnType = columns.find(c => c.name === 'type');
  const typefilterValue = filter.extraFilter?.type;
  columnType!.options!.filterList = typefilterValue ? [typefilterValue] : [];

  useEffect(() => {
    filterManager.pushHistory();

    (async () => {
      setLoading(true);
      try {
        const { search, extraFilter, ...params } = debouncedFilter as any;
        const { data } = await CastMemberResource.list({
          ...params,
          search: search?.value !== undefined ? search.value : search,
          ...(extraFilter?.type && {type: CastMemberTypesEnum[extraFilter.type]})
        });
        if (isMountedRef.current) {
          setCastMembers(data.data);
          setTotalRecords(data.meta!.total);
        }
      } catch (error) {
        if (CastMemberResource.isCancel(error)) {
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
    onFilterChange: (column, filterList, type, changedColumnIndex ) => {
      if(type === 'reset') {
        filterManager.changeExtraFilter({type: null}); 
      } else {
        filterManager.changeExtraFilter({
          [column as string] : filterList[changedColumnIndex].length ? filterList[changedColumnIndex][0] : null
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
        data={castMembers}
        loading={loading}
        options={options}
        debounceSearch={debounceSearch}
      />
    </MuiThemeProvider>
  );
};

export default CastMembersTable;
