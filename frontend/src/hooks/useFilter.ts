import {useState,useReducer} from "react";
import {reducer, INITIAL_STATE} from "../store/filter";

export const useFilter = () => {
  const [filter, dispatch] = useReducer(reducer, INITIAL_STATE);
  const [totalRecords, setTotalRecords] = useState<number>(0);

  return {
    filter,
    dispatch,
    totalRecords,
    setTotalRecords
  }
};

export default useFilter;