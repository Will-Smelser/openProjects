import com.google.common.cache.*;
import org.apache.lucene.analysis.standard.StandardAnalyzer;
import org.apache.lucene.document.*;
import org.apache.lucene.index.*;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.FSDirectory;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.jdbc.core.ColumnMapRowMapper;

import java.io.File;
import java.io.IOException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.*;
import java.util.concurrent.ExecutionException;
import java.util.concurrent.TimeUnit;

/**
 * Created by Will2 on 10/22/2016.
 */
public class DocUtil {

    private static final Logger LOGGER = LoggerFactory.getLogger(DocUtil.class);

    private static final ColumnMapRowMapper mapper = new ColumnMapRowMapper();

    public static class DocKey{
        public String id;
        public String version;
        public DocKey(String id, String version){
            this.id = id;
            this.version = version;
        }
        @Override
        public boolean equals(Object o){
            if(!(o instanceof DocKey)){
                return false;
            }
            if(((DocKey)o).id == this.id && ((DocKey)o).version == this.version){
                return true;
            }
            return false;
        }
        @Override
        public int hashCode(){
            return id.hashCode() + version.hashCode();
        }
        @Override
        public String toString(){
            return id+":"+version;
        }
    }

    private static final LoadingCache<DocKey, IndexWriter> cache =
            CacheBuilder.newBuilder()
                    .removalListener(new RemovalListener<DocKey, IndexWriter>() {
                        public void onRemoval(RemovalNotification<DocKey, IndexWriter> notification) {
                            if(notification.getCause() == RemovalCause.EXPIRED){
                                try {
                                    notification.getValue().close();
                                } catch (IOException e) {
                                    String key = notification.getKey().toString();
                                    LOGGER.error("Error closing indexer on cache removal of key "+key, e);
                                }
                            }
                        }
                    })
                    .expireAfterAccess(3, TimeUnit.DAYS)
                    .build(new CacheLoader<DocKey,IndexWriter>(){
                        @Override
                        public IndexWriter load(DocKey key) throws Exception {
                            String dirLoc = key.id + File.separator + key.version;
                            Directory dir = FSDirectory.open(new File(dirLoc).toPath());
                            return new IndexWriter(dir, new IndexWriterConfig(new StandardAnalyzer()));
                        }
                    });


    public static WriterDelegate getWriter(String id, String version) throws IOException, ExecutionException {

        //store a writer if necessary
        IndexWriter writer = cache.get(new DocKey(id,version));

        return new WriterDelegate(writer, id, version);
    }

    public static IndexReader getReader(String id, String version) throws IOException, ExecutionException {
        return getWriter(id, version).reader();
    }

    public static IndexSearcher getSearcher(String id, String version) throws IOException, ExecutionException {
        return new IndexSearcher(getReader(id, version));
    }

    public static IndexSearcher getSearcherByKeys(Set<DocKey> docs) throws IOException, ExecutionException {
        List<IndexReader> readers = new ArrayList<>();
        for(DocKey key : docs){
            readers.add(getReader(key.id, key.version));
        }

        IndexReader[] arrReaders = readers.toArray(new IndexReader[readers.size()]);
        IndexReader all = new MultiReader(arrReaders);
        return new IndexSearcher(all);
    }

    public static IndexSearcher getSearcherByIds(Set<String> ids) throws IOException, ExecutionException {
        List<IndexReader> readers = new ArrayList<>();
        for(Map.Entry<DocKey, IndexWriter> entry: cache.asMap().entrySet()){
            DocKey key = entry.getKey();
            if(ids.contains(key.id)){
                readers.add(getReader(key.id,key.version));
            }
        }

        IndexReader[] arrReaders = readers.toArray(new IndexReader[readers.size()]);
        IndexReader all = new MultiReader(arrReaders);
        return new IndexSearcher(all);
    }

    public static Document create(ResultSet row) throws SQLException {
        Map<String,Object> rowMap = mapper.mapRow(row, 0);

        return create(rowMap);
    }

    public static Document create(Map<String,?> data){
        Document doc = new Document();

        for(Map.Entry<String,?> entry : data.entrySet()) {
            doc.add(createField(entry.getKey(), entry.getValue()));
        }

        return doc;
    }

    private static IndexableField createField(String name, Object obj){
        IndexableField field;

        if(obj instanceof String){
            field = new TextField(name,(String)obj, Field.Store.YES);
        }else if(obj instanceof Number){
            long val = ((Number) obj).longValue();
            field = new NumericDocValuesField(name, val);
        }else{
            field = new TextField(name, obj.toString(), Field.Store.NO);
        }

        return field;
    }

}
